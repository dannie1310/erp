<?php


namespace App\Services\SEGUIMIENTO\Finanzas;


use App\Events\IncidenciaCI;
use App\Models\REPSEG\FinDimIngresoCliente;
use App\Models\REPSEG\FinDimIngresoEmpresa;
use App\Models\REPSEG\FinDimIngresoPartida;
use App\Models\REPSEG\FinDimTipoIngreso;
use App\Models\REPSEG\FinFacIngresoFactura;
use App\Models\REPSEG\GrlMoneda;
use App\Models\REPSEG\GrlProyecto;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Repositories\REPSEG\FacturaRepository as Repository;
use App\Utils\CFD;
use App\Utils\Util;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Storage;

class FacturaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * FacturaService constructor.
     * @param FinFacIngresoFactura $model
     */
    public function __construct(FinFacIngresoFactura $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        if (isset($data['idproyecto']))
        {
            $proyectos = GrlProyecto::where([['proyecto', 'LIKE', '%'.$data['idproyecto'].'%']])->pluck("idproyecto");
            $this->repository->whereIn(['idproyecto',  $proyectos]);
        }

        if(isset($data['numero']))
        {
            $this->repository->where([['numero', 'LIKE', '%' . $data['numero'] . '%']]);
        }

        if (isset($data['fecha_emision']))
        {
            $this->repository->whereBetween( ['fecha', [ request( 'fecha_emision' )." 00:00:00",request( 'fecha_emision' )." 23:59:59"]] );
        }

        if (isset($data['idempresa']))
        {
            $empresas = FinDimIngresoEmpresa::where([['empresa', 'LIKE', '%'.$data['idempresa'].'%']])->pluck("idempresa");
            $this->repository->whereIn(['idempresa',  $empresas]);
        }

        if (isset($data['idcliente']))
        {
            $clientes = FinDimIngresoCliente::where([['cliente', 'LIKE', '%'.$data['idcliente'].'%']])->pluck("idcliente");
            $this->repository->whereIn(['idcliente',  $clientes]);
        }

        if(isset($data['descripcion']))
        {
            $this->repository->where([['descripcion', 'LIKE', '%'.$data['descripcion'].'%']]);
        }

        if (isset($data['idmoneda']))
        {
            $monedas = GrlMoneda::where([['moneda', 'LIKE', '%'.$data['idmoneda'].'%']])->pluck("idmoneda");
            $this->repository->whereIn(['idmoneda',  $monedas]);
        }

        if(isset($data['importe']))
        {
            $this->repository->where([['importe', 'LIKE', '%'.$data['importe'].'%']]);
        }

        if (isset($data['fecha']))
        {
            $this->repository->whereBetween( ['timestamp', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }

        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function cancelar($data , $id)
    {
        return $this->repository->show($id)->cancelar($data['motivo']);
    }

    public function store($data)
    {
        try {
            if($data['xml'] != '')
            {
                $this->validaciones($data);
                $factura = $this->repository->create($data);
                $data['xml_file'] = explode("base64,", $data['xml'])[1];
                $this->guardarXML($data);
                $this->repository->registrarXML($data, $factura);
            }else{
                $factura = $this->repository->create($data);
            }
            return $factura;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function cargarArchivo($archivo_xml)
    {
        $arreglo = [];
        $cfd = new CFD($archivo_xml['facturas']);
        $arreglo_cfd = $cfd->getArregloFactura();
        if($arreglo_cfd["tipo_comprobante"] != "I")
        {
            abort(500, "Se ingresó un CFDI de tipo erróneo, favor de ingresar un CFDI de tipo ingreso (Factura)");
        }
        $arreglo['uuid'] = $arreglo_cfd['uuid'];
        $arreglo['cfdi_relacionado'] = $arreglo_cfd['cfdi_relacionado'];
        $arreglo['tipo_relacion'] = $arreglo_cfd['tipo_relacion'];
        $arreglo['nombre_archivo'] = $archivo_xml['nombre_archivo'];
        $arreglo['version'] = $arreglo_cfd['version'];
        $arreglo['xml'] = $arreglo_cfd['xml'];
        $arreglo['descuento'] = $arreglo_cfd['descuento'];
        $arreglo['total'] = $arreglo_cfd['total'];
        $arreglo['subtotal'] = $arreglo_cfd['subtotal'];
        $arreglo['iva'] = $arreglo_cfd['importe_iva'];
        $arreglo['tipo_comprobante'] = $arreglo_cfd['tipo_comprobante'];
        $arreglo['numero_factura'] = $arreglo_cfd['serie'].$arreglo_cfd['folio'];
        $arreglo['serie'] = $arreglo_cfd['serie'];
        $arreglo['folio'] = $arreglo_cfd['folio'];
        $arreglo['fecha_emision'] = $arreglo_cfd['fecha']->format('Y-m-d');
        $arreglo['fecha_inicial'] = '';
        $arreglo['fecha_fin'] = '';
        $arreglo['fecha_hora'] = $arreglo_cfd["fecha_hora"];
        $moneda = GrlMoneda::where('moneda', $arreglo_cfd['moneda'])->first();
        $arreglo['id_moneda'] = $moneda ? $moneda->getKey() : 3;
        $arreglo['tipo_cambio'] = $arreglo_cfd['tipo_cambio'] ? floatval($arreglo_cfd['tipo_cambio']) : 1.00;
        $arreglo['monedas'] = GrlMoneda::selectRaw('idmoneda as id, moneda as nombre')->orderBy('orden','ASC')->get()->toArray();
        $arreglo['no_certificado'] = $arreglo_cfd['no_certificado'];
        $arreglo["certificado"] = $arreglo_cfd["certificado"];
        $arreglo["sello"] = $arreglo_cfd["sello"];
        $empresa_sat = EmpresaSAT::where('rfc', $arreglo_cfd['emisor']['rfc'])->first();
        $arreglo['empresa_sat'] = $empresa_sat ? $empresa_sat->toArray() : $empresa_sat;
        $empresa = FinDimIngresoEmpresa::where('rfc', $arreglo_cfd['emisor']['rfc'])->first();
        $arreglo['id_empresa'] = $empresa ? $empresa->idempresa : '';
        $arreglo['empresa_rfc'] = $arreglo_cfd["emisor"]["rfc"];
        $arreglo['razon_social'] = $arreglo_cfd["emisor"]["razon_social"];
        $cliente = FinDimIngresoCliente::where('rfc', $arreglo_cfd['receptor']['rfc'])->first();
        if($cliente == null)
        {
            $cliente = $this->repository->setCliente($arreglo_cfd['receptor']);
        }
        $arreglo['id_cliente'] = $cliente->idcliente;
        $arreglo['cliente_rfc'] = $arreglo_cfd['receptor']['rfc'];
        $arreglo['nombre'] = $arreglo_cfd['receptor']['nombre'];
        $arreglo['cliente_razon_social'] = $arreglo_cfd['receptor']['razon_social'];
        $arreglo["complemento"]["uuid"] = $arreglo_cfd["uuid"];
        foreach ($arreglo_cfd['conceptos'] as $key => $concepto)
        {
            $arreglo['conceptos'][$key]['cantidad'] = $concepto['cantidad'];
            $arreglo['conceptos'][$key]['valor_unitario'] = $concepto['valor_unitario'];
            $arreglo['conceptos'][$key]['idconcepto'] = '';
            $arreglo['conceptos'][$key]['importe'] = $concepto['importe'];
            $arreglo['conceptos'][$key]['descuento'] = $concepto['descuento'] ? $concepto['descuento'] : 0.0;
        }
        $arreglo['tipoConceptos'] = FinDimTipoIngreso::activos()->orderBy('tipo_ingreso','ASC')->selectRaw('idtipo_ingreso as id, tipo_ingreso as nombre')->get()->toArray();
        $arreglo['tipos_partida'] = FinDimIngresoPartida::activos()->selectRaw('idpartida as id, partida as partida, nombre_operador')->orderBy('partida','ASC')->get()->toArray();
        $arreglo['id_proyecto'] = '';
        $arreglo['proyectos'] = '';
        $this->validaciones($arreglo);
        $arreglo = $this->validaEmpresaSAT($arreglo);
        return $arreglo;
    }

    private function validaEmpresaSAT($arreglo_cfd)
    {
        if($arreglo_cfd['empresa_sat'] == null && $arreglo_cfd['id_empresa'] != '')
        {
            abort(403, 'La emisora del CFDI '.$arreglo_cfd['razon_social'].' ('.$arreglo_cfd['empresa_rfc'].') no esta dada de alta en el
            catálogo maestro de empresas del ERP, favor de solicitar el alta a soporte a aplicaciones.');
        }
        if($arreglo_cfd['empresa_sat'] == null && $arreglo_cfd['id_empresa'] == '')
        {
            abort(403, 'La emisora del CFDI '.$arreglo_cfd['razon_social'].' ('.$arreglo_cfd['empresa_rfc'].') no esta dada de alta en el catálogo,
            favor de darla de alta y adicionalmente solicitar el alta en el catálogo maestro del ERP al área de soporte a aplicaciones.');
        }
        if($arreglo_cfd['empresa_sat'] != null && $arreglo_cfd['id_empresa'] == '')
        {
            $arreglo_cfd['id_empresa'] = $this->repository->setEmpresa($arreglo_cfd['empresa_sat']);
        }
        return $arreglo_cfd;
    }

    private function guardarXML($datos)
    {
        $xml_split = explode('base64,', $datos['xml']);
        $xml = base64_decode($xml_split[1]);
        Storage::disk('xml_emitidos')->put($datos["uuid"] . ".xml", $xml);
    }

    private function validaciones($data)
    {
        $cfdi = $this->repository->validaExistencia($data["uuid"]);
        if($cfdi)
        {
            abort(403, 'Este comprobante ya existe previamente.');
        }
        if($data["tipo_comprobante"] != "I")
        {
            abort(500, "Se ingresó un CFDI de tipo erróneo, favor de ingresar un CFDI de tipo ingreso (Factura)");
        }
    }
}
