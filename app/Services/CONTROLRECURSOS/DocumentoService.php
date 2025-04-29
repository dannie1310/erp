<?php

namespace App\Services\CONTROLRECURSOS;

use App\Events\IFS\EnvioXMLDocumentoRecursos;
use App\Models\CONTROL_RECURSOS\CtgMoneda;
use App\Models\CONTROL_RECURSOS\CuentaContableIFS;
use App\Models\CONTROL_RECURSOS\Documento;
use App\Models\CONTROL_RECURSOS\Proveedor;
use App\Models\CONTROL_RECURSOS\Serie;
use App\Models\CONTROL_RECURSOS\TipoDocto;
use App\Models\SEGURIDAD_ERP\Finanzas\CodigoImpuesto;
use App\Repositories\CONTROLRECURSOS\DocumentoRepository as Repository;
use App\Utils\CFD;
use App\Utils\Util;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Storage;
use Spatie\ArrayToXml\ArrayToXml;

class DocumentoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param Documento $model
     */
    public function __construct(Documento $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        if (isset($data['idserie']))
        {
            $serie = Serie::where([['Descripcion', 'LIKE', '%' . $data['idserie'] . '%']])->pluck('idseries');
            $this->repository->whereIn(['IdSerie', $serie]);
        }

        if (isset($data['IdProveedor']))
        {
            $proveedor = Proveedor::where([['RazonSocial', 'LIKE', '%' . $data['IdProveedor'] . '%']])->pluck('IdProveedor');
            $this->repository->whereIn(['IdProveedor', $proveedor]);
        }

        if (isset($data['idtipodocto']))
        {
            $tipo = TipoDocto::where([['Descripcion', 'LIKE', '%' . $data['idtipodocto'] . '%']])->pluck('IdTipoDocto');
            $this->repository->whereIn(['IdTipoDocto', $tipo]);
        }

        if (isset($data['Fecha']))
        {
            $this->repository->whereBetween( ['Fecha', [ request( 'Fecha' )." 00:00:00",request( 'Fecha' )." 23:59:59"]] );
        }

        if (isset($data['foliodocto']))
        {
            $this->repository->where([['FolioDocto', 'LIKE', '%'.$data['foliodocto'].'%']]);
        }

        if (isset($data['concepto']))
        {
            $this->repository->where([['Concepto', 'LIKE', '%'.$data['concepto'].'%']]);
        }

        if (isset($data['total']))
        {
            $this->repository->where([['Total', 'LIKE', '%'.$data['total'].'%']]);
        }

        if (isset($data['idmoneda']))
        {
            $tipo = CtgMoneda::where([['moneda', 'LIKE', '%' . $data['idmoneda'] . '%']])->pluck('id');
            $this->repository->whereIn(['IdMoneda', $tipo]);
        }

        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store(array $data)
    {
        $vencimiento = new DateTime($data["vencimiento"]);
        $vencimiento->setTimezone(new DateTimeZone('America/Mexico_City'));

        $fecha = new DateTime($data["fecha"]);
        $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));

        $this->validaFechas($fecha, $vencimiento);
        $data['vencimiento'] = $vencimiento->format('Y-m-d');
        $data['fecha'] = $fecha->format('Y-m-d');
        return $this->repository->registrar($data);
    }

    private function validaFechas($emision, $vencimiento)
    {
        if ($emision > $vencimiento) {
            abort(500, "La fecha de facturación no puede ser mayor a la fecha de vencimiento");
        }
    }

    public function update(array $data, $id)
    {
        return $this->repository->show($id)->editar($data);
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminar();
    }

    public function xml($id)
    {
        $documento = $this->show($id);
        $segmentos_negocio = $documento->consultaXML();
        $sumatorias = $documento->sumaConsultaXML()[0];
        if($documento->uuid) {
            $array_xml = null;
            $array_fin = [];
            $array = [];
            if ($documento->uuid) {
                $array_xml = $this->abrirXMLFactura($documento->uuid);
            }

            $condiciones = '';
            if ($array_xml != null) {
                $condiciones = (int)preg_replace("/[^0-9]/", "", $array_xml['condicion_de_pago']);
                if ($condiciones == 0 && $array_xml['condicion_de_pago'] == 'CONTADO') {
                    $condiciones = 0;
                }
            }

            $header = [
                'NAME' => 'INVOICE_HEADER',
                'C00' => 'I',
                'C01' => $documento->empresa->rfc_sin_guiones,
                'C02' => $documento->proveedor->rfc_sin_guiones,
                'C03' => '',
                'C04' => '',
                'C05' => '',
                'C06' => '',
                'C07' => $array_xml ? $array_xml['serie'] : $documento->CFDI->serie,
                'C08' => $documento->FolioDocto,
                'C09' => $array_xml ? $array_xml['moneda'] : '',
                'C10' => $condiciones,
                'N00' => $documento->TC,
                'D00' => $documento->Fecha . '-00.00.00',
                'N01' => $documento->Importe,
                'N02' => $array_xml ? $array_xml['total_impuestos_trasladados'] : $documento->OtrosImpuestos,
                'N03' => $array_xml ? $array_xml['total_impuestos_retenidos'] : $documento->Retenciones,
                'C11' => 'FALSE',
                'D01' => $documento->Fecha . '-00.00.00',
                'C12' => $documento->uuid ? $documento->uuid : '',
                'C13' => utf8_decode(substr($documento->solChequeDocto->solCheque->Concepto, 0, 120)),
                'C14' => $documento->uuid ? $documento->uuid . '.xml' : $documento->FolioDocto,
                'C15' => auth()->user()->usuario,
                'C16' => str_replace('-', ' ', $documento->folio_solicitud),
            ];

            if ($array_xml != null) {
                $total_traslados = 0;
                $total_retenido = 0;
                $k = 0;
                $i = 0;
                foreach ($array_xml['conceptos'] as $key => $concepto) {
                    $k = $i;
                    $array[$i] = [
                        'NAME' => 'INVOICE_ITEM',
                        'N00' => $key + 1,
                        'N01' => $concepto['importe'],
                        'N02' => $total_traslados,
                        'N03' => $total_retenido,
                        'C00' => '',
                        'C01' => '',
                        'C02' => $concepto['unidad'],
                        'N04' => $concepto['cantidad'],
                        'C03' => $concepto['clave_prod_serv'],
                        'C04' => $concepto['descripcion'],
                        'N05' => $concepto['valor_unitario'],
                        'C05' => 'FALSE',
                        'C06' => '',
                        'N06' => '',
                        'N07' => '',
                        'C07' => ''
                    ];
                    $i++;

                    if (array_key_exists('traslados', $concepto)) {
                        foreach ($concepto['traslados'] as $traslado) {
                            $total_traslados += $traslado['importe'];
                            $codigo = CodigoImpuesto::where('codigo_sat', $traslado['impuesto'])->where('tipo_codigo','I')->first();
                            $array[$i] = [
                                'NAME' => 'INVOICE_ITEM_TAX',
                                'N00' => $key + 1,
                                'C00' => $codigo ? $codigo->codigo_ifs : $traslado['impuesto'],
                                'N01' => $traslado['tasa_o_cuota'] * 100,
                                'N02' => $traslado['importe']
                            ];
                            $i++;
                        }
                    }
                    if (array_key_exists('retenciones', $concepto)) {
                        foreach ($concepto['retenciones'] as $retencion) {
                            $total_retenido += $retencion['importe'];
                            $codigo = CodigoImpuesto::where('codigo_sat', $retencion['impuesto'])->where('tipo_codigo','R')->first();
                            $array[$i] = [
                                'NAME' => 'INVOICE_ITEM_TAX',
                                'N00' => $key + 1,
                                'C00' => $codigo ? $codigo->codigo_ifs : $retencion['impuesto'],
                                'N01' => $retencion['tasa_o_cuota']*100,
                                'N02' => $retencion['importe']
                            ];
                            $i++;
                        }
                    }

                    if(count($segmentos_negocio) > 0) {
                        foreach ($segmentos_negocio as $item) {
                            $porcentaje = $item->importe_segmento / $sumatorias->importe_segmento;
                            $importe = $porcentaje * $concepto['importe'];
                            $cuenta = CuentaContableIFS::where('id_tipo_gasto', $item->id_tipo_gasto)->first();
                            dd($cuenta, $item);
                            $array[$i] = [
                                'NAME' => 'INVOICE_ITEM_POSTING',
                                'N00' => $key + 1,
                                'N01' => $importe,
                                'C00' => Util::eliminaAcentos($item->segmento_negocio),
                                'C01' => '',
                                'C02' => $cuenta ? $cuenta->cuenta_ifs : '',
                                'C03' => '',
                                'C04' => '',
                                'C05' => '',
                                'C06' => $item->NoSN,
                                'C07' => '',
                                'C08' => '',
                                'C09' => '',
                                'C10' => '',
                                'N02' => '',
                            ];
                            $i++;
                        }
                    }
                    $array[$k]['N02'] = $total_traslados;
                    $array[$k]['N03'] = $total_retenido;
                }
            }

            $array_fin = [
                'CLASS_ID' => 'INVHI',
                'RECEIVER' => 'IFS_APPLICATIONS',
                'SENDER' => 'SISTEMA_CONTROL_RECURSOS',
                'LINES' => [
                    'IN_MESSAGE_LINE' => [
                        $header,
                        $array
                    ],
                ]
            ];
            $a = new ArrayToXml($array_fin, "IN_MESSAGE");
            $a->setDomProperties(['formatOutput' => true]);
            $result = $a->toXml();
            $name = $documento->uuid ? $documento->uuid : $documento->folio_solicitud;
            $this->buscarXML('archivo_ifs_' .$name . '.xml');
            Storage::disk('ifs_solicitud_recurso')->put('archivo_ifs_' . $name . '.xml', $result);
            return Storage::disk('ifs_solicitud_recurso')->download('archivo_ifs_' . $name . '.xml');
        }else{
            abort(500, "Documento sin CFDI.");
        }
    }

    public function abrirXMLFactura($nombre_archivo)
    {
        if(Storage::disk('xml_control_recursos')->exists($nombre_archivo.'.xml')) {
            $archivo = Storage::disk('xml_control_recursos')->get($nombre_archivo . '.xml');
            $cfd = new CFD($archivo);
            return $cfd->getArregloFactura();
        }
        return null;
    }

    public function correo($id)
    {
        $documento = $this->show($id);
        $name = $documento->uuid ? $documento->uuid : $documento->folio_solicitud;
        $this->xml($id);
        $archivo = $this->getBase64XML($documento->uuid);
        event(new EnvioXMLDocumentoRecursos($documento, config('app.env_variables.EMAIL_IFS'), 'archivo_ifs' . $documento->uuid . '.xml', $archivo));
    }

    private function getBase64XML($name)
    {
        if (Storage::disk('ifs_solicitud_recurso')->exists('archivo_ifs_'.$name.'.xml'))
        {
            $archivo = Storage::disk("ifs_solicitud_recurso")->get('archivo_ifs_'.$name.'.xml');
            return "data:text/xml;base64,".base64_encode($archivo);
        }
        return null;
    }

    private function buscarXML($nombre)
    {
        if(Storage::disk('ifs_solicitud_recurso')->exists($nombre)) {
            Storage::disk('ifs_solicitud_recurso')->delete($nombre);
        }
    }
}
