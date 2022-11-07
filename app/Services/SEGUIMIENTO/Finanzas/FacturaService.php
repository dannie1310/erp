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
use App\Repositories\REPSEG\FacturaRepository as Repository;
use App\Utils\CFD;
use App\Utils\Util;

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

        if (isset($data['fecha']))
        {
            $this->repository->whereBetween( ['fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
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

        if (isset($data['fecha_cobro']))
        {
            $this->repository->whereBetween( ['fecha_cobro', [ request( 'fecha_cobro' )." 00:00:00",request( 'fecha_cobro' )." 23:59:59"]] );
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
            return $this->repository->create($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function cargarArchivo($archivo_xml)
    {

        $arreglo = [];
        $cfd = new CFD($archivo_xml['facturas']);
        $arreglo_cfd = $cfd->getArregloFactura();
        $arreglo['version'] = $arreglo_cfd['version'];
        $arreglo['xml'] = $arreglo_cfd['xml'];
        $arreglo['descuento'] = $arreglo_cfd['descuento'];
        $arreglo['total'] = $arreglo_cfd['total'];
        $arreglo['subtotal'] = $arreglo_cfd['subtotal'];
        $arreglo['tipo_comprobante'] = $arreglo_cfd['tipo_comprobante'];
        $arreglo['numero_factura'] = $arreglo_cfd['serie'].$arreglo_cfd['folio'];
        $arreglo['fecha_emision'] = $arreglo_cfd['fecha']->format("Y-m-d");
        $arreglo['fecha_hora'] = $arreglo_cfd['fecha_hora'];
        $moneda = GrlMoneda::where('moneda', $arreglo_cfd['moneda'])->first();
        $arreglo['id_moneda'] = $moneda ? $moneda->getKey() : 3;
        $arreglo['tipo_cambio'] = $arreglo_cfd['tipo_cambio'] ? floatval($arreglo_cfd['tipo_cambio']) : 1.00;
        $arreglo['monedas'] = GrlMoneda::selectRaw('idmoneda as id, moneda as nombre')->orderBy('orden','ASC')->get()->toArray();
        $arreglo['no_certificado'] = $arreglo_cfd['no_certificado'];
        $arreglo["certificado"] = $arreglo_cfd["certificado"];
        $arreglo["sello"] = $arreglo_cfd["sello"];
        $nombre_empresa = Util::eliminaCaracteresEspeciales($arreglo_cfd['emisor']['razon_social']);
        $empresa = FinDimIngresoEmpresa::where('empresa', 'like', '%'.$nombre_empresa.'%')->first();
        $arreglo['id_empresa'] = $empresa ? $empresa->idempresa : '';
        $empresas = FinDimIngresoEmpresa::activos()->selectRaw('idempresa as id, empresa as nombre')->orderBy('empresa','ASC')->get();
        $arreglo['empresas'] = $empresas->toArray();
        $arreglo['empresa_rfc'] = $arreglo_cfd["emisor"]["rfc"];
        $arreglo['razon_social'] = $arreglo_cfd["emisor"]["razon_social"];
        $nombre_cliente = Util::eliminaCaracteresEspeciales($arreglo_cfd['receptor']['razon_social']);
        $cliente = FinDimIngresoCliente::where('cliente', 'like', '%'.$nombre_cliente.'%')->first();
        $arreglo['id_cliente'] = $cliente ? $cliente->idcliente : '';
        $clientes = FinDimIngresoCliente::activos()->selectRaw('idcliente as id, cliente as nombre')->orderBy('cliente','ASC')->get();
        $arreglo['clientes'] = $clientes->toArray();
        $arreglo['cliente_rfc'] = $arreglo_cfd['receptor']['rfc'];
        $arreglo['nombre'] = $arreglo_cfd['receptor']['nombre'];
        $arreglo["complemento"]["uuid"] = $arreglo_cfd["uuid"];
        foreach ($arreglo_cfd['conceptos'] as $key => $concepto)
        {
            $arreglo['conceptos'][$key]['idconcepto'] = '';
            $arreglo['conceptos'][$key]['importe'] = $concepto['importe'];
            if(array_key_exists('descuento',$concepto))
            {
                $arreglo['conceptos'][$key]['descuento'] = $concepto['descuento'];
                $arreglo['partidas'][$key]['idpartida'] = '';
                $arreglo['partidas'][$key]['antes_iva'] = false;
                $arreglo['partidas'][$key]['total'] = $concepto['descuento'];
            }
        }
        $arreglo['tipoConceptos'] = FinDimTipoIngreso::activos()->orderBy('tipo_ingreso','ASC')->selectRaw('idtipo_ingreso as id, tipo_ingreso as nombre')->get()->toArray();
        $arreglo['tipos_partida'] = FinDimIngresoPartida::activos()->selectRaw('idpartida as id, partida as partida, nombre_operador')->orderBy('partida','ASC')->get()->toArray();
        $arreglo['id_proyecto'] = '';
        $arreglo['proyectos'] = '';
        //$this->validaEFO($arreglo);
        //$this->validaReceptor($arreglo);








        return $arreglo;
    }

    public function cargaXML(array $data)
    {
        $archivo_xml = $data["xml"];
        $tipo = $data["tipo"];
        $id_empresa = $data["id_empresa"];
        $arreglo_cfd = $this->getArregloCFD($archivo_xml);
        if(is_numeric($id_empresa)){
            $empresa = $this->repository->getEmpresaPorId($id_empresa);
            if($empresa["rfc"] != $arreglo_cfd["emisor"]["rfc"]){
                if($arreglo_cfd["tipo_comprobante"] == "E"){
                    abort(500, "El emisor de los CFDI no coincide, favor de verificar");
                }
            }
        }
        if($arreglo_cfd["tipo_comprobante"] == "I" && $tipo == 2)
        {
            abort(500, "Se ingresó un CFDI de tipo erróneo, favor de ingresar un CFDI de tipo egreso (Nota de Crédito)");
        }
        elseif($arreglo_cfd["tipo_comprobante"] == "E" && $tipo == 1)
        {
            abort(500, "Se ingresó un CFDI de tipo erróneo, favor de ingresar un CFDI de tipo ingreso (Factura)");
        }
        return $arreglo_cfd;
    }

    private function validaEFO($arreglo_cfd)
    {
        $efo = $this->repository->getEFO($arreglo_cfd['empresa_rfc']);
        if ($efo) {
            if ($efo->estado == 0) {
                event(new IncidenciaCI(
                    ["id_tipo_incidencia" => 8,
                        "id_empresa" => $arreglo_cfd["empresa_bd"]["id_empresa"],
                        "rfc" => $arreglo_cfd["empresa_bd"]["rfc"],
                        "empresa" => $arreglo_cfd["empresa_bd"]["razon_social"]]
                ));
                abort(403, 'La empresa que emitió el comprobante esta invalidada por el SAT, no se pueden tener operaciones con esta empresa.
             Favor de comunicarse con el área fiscal para cualquier aclaración.');
            } else if ($efo->estado == 2) {
                event(new IncidenciaCI(
                    ["id_tipo_incidencia" => 9,
                        "id_empresa" => $arreglo_cfd["empresa_bd"]["id_empresa"],
                        "rfc" => $arreglo_cfd["empresa_bd"]["rfc"],
                        "empresa" => $arreglo_cfd["empresa_bd"]["razon_social"]]
                ));
                abort(403, 'La empresa que emitió el comprobante esta invalidada por el SAT, no se pueden tener operaciones con esta empresa.
             Favor de comunicarse con el área fiscal para cualquier aclaración.');
            }

        }
    }

    private function validaPresuntoEFO($arreglo_cfd)
    {
        $efo = $this->repository->getEFO($arreglo_cfd['cliente_rfc']);
        if ($efo) {
            if ($efo->estado == 2) {
                event(new IncidenciaCI(
                    ["id_tipo_incidencia" => 9,
                        "id_empresa" => $arreglo_cfd["empresa_bd"]["id_empresa"],
                        "rfc" => $arreglo_cfd["empresa_bd"]["rfc"],
                        "empresa" => $arreglo_cfd["empresa_bd"]["razon_social"]]
                ));
            }

        }
    }
}
