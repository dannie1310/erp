<?php

namespace App\Services\CONTROLRECURSOS;

use App\Events\IFS\EnvioXMLDocumentoRecursos;
use App\Models\CONTROL_RECURSOS\CtgMoneda;
use App\Models\CONTROL_RECURSOS\CuentaContableIFS;
use App\Models\CONTROL_RECURSOS\Documento;
use App\Models\CONTROL_RECURSOS\Proveedor;
use App\Models\CONTROL_RECURSOS\Serie;
use App\Models\CONTROL_RECURSOS\TipoDocto;
use App\Repositories\CONTROLRECURSOS\DocumentoRepository as Repository;
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

        $array_segmento = [];

        foreach ($segmentos_negocio as $key => $item)
        {
            $cuenta = CuentaContableIFS::where('id_tipo_gasto', $item->id_tipo_gasto)->first();
            $array_segmento [$key] = [
                'NAME' => 'INVOICE_ITEM_POSTING',
                'N00' => 1,
                'N01' => $item->importe_segmento,
                'C00' => utf8_decode($item->segmento_negocio),
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
        }

        $array = [
            'CLASS_ID' => 'INVHI',
            'RECEIVER' => 'IFS_APPLICATIONS',
            'SENDER' => 'SISTEMA_CONTROL_RECURSOS',
            'LINES' => [
                'IN_MESSAGE_LINE' => [
                    [
                        'NAME' => 'INVOICE_HEADER',
                        'C00' => 'I',
                        'C01' => $documento->empresa->rfc_sin_guiones,
                        'C02' => $documento->proveedor->rfc_sin_guiones,
                        'C03' => '',
                        'C04' => '',
                        'C06' => '',
                        'C07' => $documento->Alias_Depto,
                        'C08' => $documento->FolioDocto,
                        'C09' => $documento->moneda->corto,
                        'N00' => $documento->TC,
                        'D00' => $documento->Fecha.'-00.00.00',
                        'C10' => '0',
                        'N01' => $documento->Total,
                        'N02' => $documento->OtrosImpuestos,
                        'N03' => $documento->Retenciones,
                        'C11' => 'FALSE',
                        'D01' => $documento->Fecha.'-00.00.00',
                        'C12' => $documento->uuid ? $documento->uuid : '',
                        'C13' => utf8_decode($documento->Concepto),
                        'C14' => $documento->uuid ? $documento->uuid .'.xml' : $documento->FolioDocto,
                        'C15' => auth()->user()->usuario,
                        'C16' => str_replace('-', '', $documento->folio_solicitud),
                    ],
                    [
                        'NAME' => 'INVOICE_ITEM',
                        'N00' => 1,
                        'N01' => $documento->Importe,
                        'N02' => $documento->IVA,
                        'N03' => 0,
                        'C00' => '',
                        'C01' => '',
                        'C02' => '',
                        'N04' => 1,
                        'C03' => '',
                        'C04' => '',
                        'N05' => $documento->Total,
                        'C05' => 'FALSE',
                        'C06' => '',
                        'N06' => '',
                        'N07' => '',
                        'C07' => ''
                    ],
                    [
                        'NAME' => 'INVOICE_ITEM_TAX',
                        'N00' => '1',
                        'C00' => 'IVA16',
                        'N01' => '16',
                        'N02' => $documento->IVA
                    ],
                    $array_segmento,
                ],
            ]
        ];

        $a = new ArrayToXml($array, "IN_MESSAGE");
        $a->setDomProperties(['formatOutput' => true]);
        $result = $a->toXml();
        $name = $documento->uuid ? $documento->uuid : $documento->folio_solicitud;
        Storage::disk('ifs_solicitud_recurso')->put(  'archivo_ifs_'.$name.'.xml', $result);
        return Storage::disk('ifs_solicitud_recurso')->download(  'archivo_ifs_'.$name.'.xml');
    }

    public function correo($id)
    {
        $documento = $this->show($id);
        $name = $documento->uuid ? $documento->uuid : $documento->folio_solicitud;
        $archivo = $this->getBase64XML($name);
        if($archivo != null) {
            event(new EnvioXMLDocumentoRecursos($documento, config('app.env_variables.EMAIL_IFS'), 'archivo_ifs' . $documento->uuid . '.xml', $archivo));
        }else{
            $this->xml($id);
            $archivo = $this->getBase64XML($documento->uuid);
            event(new EnvioXMLDocumentoRecursos($documento, config('app.env_variables.EMAIL_IFS'), 'archivo_ifs' . $documento->uuid . '.xml', $archivo));
        }
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
}
