<?php

namespace App\Services\CONTROLRECURSOS;

use App\Http\Requests\SatQueryRequest;
use App\Models\CONTROL_RECURSOS\CtgMoneda;
use App\Models\CONTROL_RECURSOS\Empresa;
use App\Models\CONTROL_RECURSOS\Proveedor;
use App\Models\CONTROL_RECURSOS\RelacionGasto;
use App\Models\CONTROL_RECURSOS\RelacionGastoDocumento;
use App\Models\CONTROL_RECURSOS\Serie;
use App\Models\CONTROL_RECURSOS\VwUbicacionRelacion;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\PDF\ControlRecursos\RelacionGastosFormato;
use App\Repositories\CONTROLRECURSOS\RelacionGastoRepository as Repository;
use App\Services\SEGURIDAD_ERP\Contabilidad\CFDSATService;
use App\Utils\CFD;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RelacionGastoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param RelacionGasto $model
     */
    public function __construct(RelacionGasto $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        if (isset($data['idserie'])) {
            $serie = Serie::where([['Descripcion', 'LIKE', '%' . $data['idserie'] . '%']])->pluck('idseries');
            $this->repository->whereIn(['IdSerie', $serie]);
        }

        if (isset($data['idempleado'])) {
            $proveedor = Proveedor::where([['RazonSocial', 'LIKE', '%' . $data['idempleado'] . '%']])->pluck('IdProveedor');
            $this->repository->whereIn(['idempleado', $proveedor]);
        }

        if (isset($data['idempresa'])) {
            $proveedor = Empresa::where([['RazonSocial', 'LIKE', '%' . $data['idempresa'] . '%']])->pluck('IdEmpresa');
            $this->repository->whereIn(['idempresa', $proveedor]);
        }

        if (isset($data['fecha_inicio'])) {
            $this->repository->whereBetween(['fecha_inicio', [request('fecha_inicio') . " 00:00:00", request('fecha_iniciov') . " 23:59:59"]]);
        }

        if (isset($data['folio'])) {
            $this->repository->where([['folio', 'LIKE', '%' . $data['folio'] . '%']]);
        }

        if (isset($data['idproyecto'])) {
            $proveedor = VwUbicacionRelacion::where([['ubicacion', 'LIKE', '%' . $data['idproyecto'] . '%']])->pluck('idubicacion');
            $this->repository->whereIn(['idproyecto', $proveedor]);
        }

        if (isset($data['idmoneda'])) {
            $tipo = CtgMoneda::where([['moneda', 'LIKE', '%' . $data['idmoneda'] . '%']])->pluck('id');
            $this->repository->whereIn(['idmoneda|      ||  ', $tipo]);
        }

        return $this->repository->paginate($data);
    }

    public function store($data)
    {
        $fecha_inicial = new DateTime($data['fecha_inicial']);
        $fecha_inicial->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data['fecha_inicial'] = $fecha_inicial->format("Y-m-d H:i:s");
        $fecha_final = new DateTime($data['fecha_final']);
        $fecha_final->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data['fecha_final'] = $fecha_final->format("Y-m-d H:i:s");

        try {
            DB::connection('controlrec')->beginTransaction();

            $relacion = $this->repository->registrar($data);

            foreach ($data['partidas'] as $partida) {
                $fecha = new DateTime($partida['fecha_editar']);
                $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
                $partida['fecha'] = $fecha->format("Y-m-d");
                if ($partida['uuid'] != null) {
                    $this->validaCFDI($partida['uuid']);
                    $arreglo_cfd = $this->getArregloCFD($partida['xml']);
                    $this->validaReceptor($this->repository->getBuscarEmpresa($relacion->idempresa),$arreglo_cfd);
                    $documento = $relacion->registrarDocumento($partida);
                    $factura_repositorio = $this->registrarCFDRepositorio($documento, $arreglo_cfd, $partida['xml']);
                    $this->registrarCFDSAT($factura_repositorio, $arreglo_cfd);
                    $this->guardarXML($partida);
                } else {
                    $relacion->registrarDocumento($partida);
                }
            }
            DB::connection('controlrec')->commit();
            return $relacion;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function validaCFDI($uuid)
    {
        $cfdi = CFDSAT::where('uuid', $uuid)->first();
        $factura = FacturaRepositorio::where('uuid', $uuid)->first();
        if ($factura && $cfdi) {
            if ($factura->id_transaccion != null || $factura->id_documento_cr != null || $factura->id_doc_relacion_gastos_cr != null) {
                abort(500, "El CFDI " . $uuid . " fue utilizado anteriormente.");
            }
        }
    }

    private function validaReceptor($empresa, $arreglo)
    {
        if($empresa->RFC != $arreglo['receptor']['rfc'])
        {
            abort(500, "El receptor (".$arreglo["receptor"]["rfc"].") del comprobante no es la misma empresa (".$empresa->RFC.") seleccionada para esta relación de gastos; la factura no puede ser registrada.");
        }

    }

    private function getArregloCFD($archivo_xml)
    {
        $arreglo = [];
        $cfd = new CFD($archivo_xml);
        $arreglo_cfd = $cfd->getArregloFactura();
        $this->validaUUIDDocumento($arreglo_cfd["uuid"], $arreglo_cfd["tipo_comprobante"]);
        $arreglo["total"] = $arreglo_cfd["total"];
        $arreglo["impuesto"] = $arreglo_cfd["total_impuestos_trasladados"];
        $arreglo["tipo_comprobante"]  = $arreglo_cfd["tipo_comprobante"];
        $arreglo["retencion"]  = $arreglo_cfd["descuento"];
        $arreglo["serie"] = $arreglo_cfd["serie"];
        $arreglo["otros"] = $arreglo_cfd["total_impuestos_retenidos"];
        $arreglo["folio"] = $arreglo_cfd["folio"];
        $arreglo["fecha"] = $arreglo_cfd["fecha"]->format("m/d/Y");
        $arreglo["vencimiento"] = $arreglo_cfd["fecha"]->format("m/d/Y");
        $arreglo["fecha_hora"] = $arreglo_cfd["fecha_hora"];
        $arreglo["version"] = $arreglo_cfd["version"];
        $arreglo["moneda"] = $arreglo_cfd["moneda"];
        $arreglo["no_certificado"] = $arreglo_cfd["no_certificado"];
        $arreglo["certificado"] = $arreglo_cfd["certificado"];
        $arreglo["sello"] = $arreglo_cfd["sello"];
        $arreglo["concepto"] = $arreglo_cfd['conceptos'][0]['descripcion'];

        $arreglo["emisor"]["rfc"] = $arreglo_cfd["emisor"]["rfc"];
        $arreglo["emisor"]["nombre"] = $arreglo_cfd["emisor"]["nombre"];

        $arreglo["receptor"]["rfc"] = $arreglo_cfd["receptor"]["rfc"];
        $arreglo["receptor"]["nombre"] = $arreglo_cfd["receptor"]["nombre"];

        $arreglo["complemento"]["uuid"] = $arreglo_cfd["uuid"];
        $arreglo["folio"] = $arreglo_cfd["folio"];

        $arreglo["proveedor_bd"] = $this->repository->getProveedor([
            "rfc" => $arreglo["emisor"]["rfc"],
            "razon_social" => $arreglo["emisor"]["nombre"]
        ]);

        $arreglo["id_proveedor"] = $arreglo['proveedor_bd'] != null ? $arreglo["proveedor_bd"]['id'] : '';

        $arreglo["empresa_bd"] = $this->repository->getEmpresa([
            "rfc" => $arreglo["receptor"]["rfc"],
            "razon_social" => $arreglo["receptor"]["nombre"]
        ]);

        $arreglo["id_empresa"] = $arreglo['empresa_bd'] != null ? $arreglo["empresa_bd"]['id'] : '';

        $this->validaEFO($arreglo);
        if (!$arreglo["empresa_bd"]) {
            abort(500, "El receptor (".$arreglo["receptor"]["rfc"].") del comprobante no esta dado de alta en el catálogo de empresas de control recursos; la factura no puede ser registrada.");
        }
        $arreglo["moneda_bd"]["id_moneda"] = $this->repository->getMoneda($arreglo["moneda"]);
        $arreglo["id_moneda"] = $this->repository->getMoneda($arreglo["moneda"]);
        $arreglo['tipo_cambio'] = $arreglo_cfd['tipo_cambio'];
        $arreglo["subtotal"] = $arreglo_cfd["subtotal"];
        $arreglo["descuento"] = $arreglo_cfd["descuento"];
        $arreglo["tasa_iva"] = $arreglo_cfd["tasa_iva"] * 100;
        $arreglo["uuid"] = $arreglo_cfd["uuid"];
        return $arreglo;
    }

    private function validaUUIDDocumento($uuid, $tipo)
    {
        $repositorio_factura = $this->repository->buscarRepositorioFactura($uuid);
        if ($repositorio_factura) {
            $documento = $this->repository->buscarDocumentoUuid($uuid);
            $relacion = $this->repository->buscarRelacionGastosUuid($uuid);
            if ($relacion && $repositorio_factura->id_doc_relacion_gastos_cr != null) {
                abort(500, "CFDI utilizado previamente:
                                Registró: " . $repositorio_factura->usuario->nombre_completo . "
                                Serie: " . $relacion->relacion->serie_descripcion . "
                                Folio: " . $relacion->folio . "
                                Fecha Registro: " . $relacion->fecha_format . "
                                UUID: " . $uuid . "
                                Emisor: " . $relacion->relacion->proveedor_descripcion . "
                                RFC Emisor: " . $relacion->relacion->rfc_proveedor);
            }

            if ($documento && $repositorio_factura->id_documento_cr != null) {
                abort(500, "CFDI utilizado previamente:
                                Registró: " . $repositorio_factura->usuario->nombre_completo . "
                                Serie: " . $documento->serie_descripcion . "
                                Folio: " . $documento->FolioDocto . "
                                Fecha Registro: " . $documento->fecha_format . "
                                UUID: " . $uuid . "
                                Emisor: " . $documento->proveedor_descripcion . "
                                RFC Emisor: " . $documento->rfc_proveedor);
            }
            if ($repositorio_factura && $repositorio_factura->id_transaccion != null) {
                abort(500, "CFDI utilizado previamente:
                                Registró: " . $repositorio_factura->usuario->nombre_completo . "
                                DB: " . $repositorio_factura->proyecto->base_datos . "
                                Proyecto: " . $repositorio_factura->obra . "
                                Tipo Transacción: " . $repositorio_factura->transaccion->tipo_transaccion_str . "
                                Folio Transacción: " . $repositorio_factura->transaccion->numero_folio . "
                                Fecha Registro: " . $repositorio_factura->fecha_hora_registro_format . "
                                UUID: " . $uuid . "
                                Emisor: " . $repositorio_factura->proveedor->razon_social . "
                                RFC Emisor: " . $repositorio_factura->proveedor->rfc);
            }
        }
        if ($tipo != 'I') {
            abort(500, 'El CFDI ingresado no es de un tipo válido, favor de subir CFDI’s tipo I (Ingreso) únicamente.');
        }
    }

    private function validaEFO($arreglo_cfd)
    {
        $efo = $this->repository->getEFO($arreglo_cfd["emisor"]["rfc"]);
        if ($efo) {
            if ($efo->estado == 0) {
                abort(403, 'La empresa que emitió el comprobante esta invalidada por el SAT, no se pueden tener operaciones con esta empresa.
             Favor de comunicarse con el área fiscal para cualquier aclaración.');
            } else if ($efo->estado == 2) {
                abort(403, 'La empresa que emitió el comprobante esta invalidada por el SAT, no se pueden tener operaciones con esta empresa.
             Favor de comunicarse con el área fiscal para cualquier aclaración.');
            }

        }
    }

    public function validaCFDI33($xml, $arreglo_cfd)
    {
        $usa_servicio = config('app.env_variables.SERVICIO_CFDI_EN_USO');
        if ($usa_servicio == 1) {
            $respuesta = $this->getValidacionCFDI33($arreglo_cfd);
            $cfd = new CFD($xml);
            $this->arreglo_factura = $cfd->getArregloFactura();

            if ($respuesta->Estado == 'No Encontrado') {
                $omitido = $this->repository->getEsOmitido($respuesta->Estado, $arreglo_cfd["emisor"]["rfc"], $arreglo_cfd["complemento"]["uuid"]);
                if ($omitido == 0) {
                    abort(500, "Aviso SAT:\nError al encontrar el comprobante: " . $respuesta->Estado);
                }
            }
            if ($respuesta->CodigoEstatus == "N - 601: La expresión impresa proporcionada no es válida.") {
                $omitido = $this->repository->getEsOmitido($respuesta->CodigoEstatus, $arreglo_cfd["emisor"]["rfc"], $arreglo_cfd["complemento"]["uuid"]);
                if ($omitido == 0) {
                    abort(500, "Aviso SAT:\nError en la validación de la estructura del comprobante: " . $respuesta->CodigoEstatus . ' Estado: ' . $respuesta->Estado);
                }
            }
            if ($respuesta->Estado == 'Cancelado') {
                $omitido = $this->repository->getEsOmitido($respuesta->Estado, $arreglo_cfd["emisor"]["rfc"], $arreglo_cfd["complemento"]["uuid"]);
                if ($omitido == 0) {
                    abort(500, "Aviso SAT:\nError el comprobante se encuentra: " . $respuesta->Estado);
                }
            }
            if ($respuesta->EstatusCancelacion != [] && $respuesta->EstatusCancelacion == 'En proceso') {
                $omitido = $this->repository->getEsOmitido($respuesta->EstatusCancelacion, $arreglo_cfd["emisor"]["rfc"], $arreglo_cfd["complemento"]["uuid"]);
                if ($omitido == 0) {
                    abort(500, "Aviso SAT:\nError en la validación del comprobante: " . $respuesta->EstatusCancelacion);
                }
            }
        }
    }

    private function getValidacionCFDI33($arreglo_cfd)
    {
        return SatQueryRequest::soapRequest($arreglo_cfd['emisor']['rfc'], $arreglo_cfd['receptor']['rfc'],$arreglo_cfd['total'], $arreglo_cfd['complemento']['uuid'], substr($arreglo_cfd['sello'],-8));
    }

    private function registrarCFDRepositorio($relacion, $data, $xml)
    {
        $factura_repositorio = FacturaRepositorio::where("uuid","=",$data["uuid"])->first();
        if($factura_repositorio){
            if($factura_repositorio->id_transaccion == null && $factura_repositorio->id_documento_cr == null && $factura_repositorio->id_doc_relacion_gastos_cr == null)
            {
                $factura_repositorio->id_doc_relacion_gastos_cr = $relacion->getKey();
                $factura_repositorio->save();
            }else{
                abort(500, "El CFDI fue utilizado anteriormente.");
            }
        }
        else{
            if($data){
                $factura_repositorio = FacturaRepositorio::create([
                    'xml_file' => $this->repository->getArchivoSQL($xml),
                    'hash_file' => hash_file('md5',$xml),
                    'uuid' => $data['uuid'],
                    'rfc_emisor' => $data['emisor']['rfc'],
                    'rfc_receptor' => $data['receptor']['rfc'],
                    'tipo_comprobante' => $data['tipo_comprobante'],
                    'id_doc_relacion_gastos_cr' => $relacion->getKey(),

                ]);
                if (!$factura_repositorio) {
                    abort(400, "Hubo un error al registrar el CFDI en el repositorio");
                }
            }
        }
        return $factura_repositorio;
    }

    private function registrarCFDSAT($facturaRepositorio, $data)
    {
        $servicio_cfdi = new CFDSATService(new CFDSAT());
        $servicio_cfdi->procesaFacturaRepositorio($facturaRepositorio);
    }

    private function guardarXML($datos)
    {
        $xml_split = explode('base64,', $datos['xml']);
        $xml = base64_decode($xml_split[1]);
        Storage::disk('xml_control_recursos_relacion_gastos')->put($datos["uuid"] . ".xml", $xml);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function update(array $data, $id)
    {
        $fecha_inicial = new DateTime($data['fecha_inicio_editar']);
        $fecha_inicial->setTimezone(new DateTimeZone('America/Mexico_City'));
        $fecha_final = new DateTime($data['fecha_final_editar']);
        $fecha_final->setTimezone(new DateTimeZone('America/Mexico_City'));

        try {
            DB::connection('controlrec')->beginTransaction();

            $relacion = $this->repository->show($id);
            $documentos = $relacion->documentos->pluck('idrelaciones_gastos_documentos')->toArray();

            foreach ($data['documentos']['data'] as $partida) {
                $fecha = new DateTime($partida['fecha']);
                $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
                $partida['fecha'] = $fecha->format("Y-m-d");

                if(array_key_exists('id', $partida))
                {
                    $documento = RelacionGastoDocumento::find($partida['id']);
                    if($partida['uuid'] != null){
                        $documento->update([
                            'idtipo_gasto_comprobacion' => $partida['idtipogasto'],
                            'no_personas' => $partida['no_personas'],
                            'observaciones' => $partida['observaciones']
                        ]);
                    }
                    else {
                        $documento->update([
                            'fecha' => $partida['fecha'],
                            'folio' => $partida['folio'],
                            'idtipo_docto_comp' => $partida['idtipo'],
                            'idtipo_gasto_comprobacion' => $partida['idtipogasto'],
                            'no_personas' => $partida['no_personas'],
                            'importe' => $partida['importe'],
                            'iva' => $partida['iva'],
                            'retenciones' => $partida['retenciones'],
                            'otros_impuestos' => $partida['otros_imp'],
                            'total' => $partida['total'],
                            'observaciones' => $partida['observaciones']
                        ]);
                    }
                    $key = array_search($partida['id'],$documentos, true);
                    if($key !== false)
                    {
                        unset($documentos[$key]);
                    }
                }else {
                    $partida['IVA'] = $partida['iva'];
                    $partida['otro_imp'] = $partida['otros_imp'];
                   if ($partida['uuid'] != null) {
                       $arreglo_cfd = $this->getArregloCFD($partida['xml']);
                       $relacion->update([
                           'idempresa' => $data['id_empresa']
                       ]);
                       $this->validaCFDI($partida['uuid']);
                       $this->validaReceptor($this->repository->getBuscarEmpresa($relacion->idempresa), $arreglo_cfd);
                       $documento = $relacion->registrarDocumento($partida);
                       $factura_repositorio = $this->registrarCFDRepositorio($documento, $arreglo_cfd, $partida['xml']);
                       $this->registrarCFDSAT($factura_repositorio, $arreglo_cfd);
                       $this->guardarXML($partida);
                   }else {
                       $relacion->registrarDocumento($partida);
                   }
                }
            }

            if(count($documentos) > 0)
            {
                foreach ($documentos as $documento) {
                    $d = RelacionGastoDocumento::find($documento);
                    if($d->uuid != null)
                    {
                        $this->desvinculaFacturaRepositorio($d->uuid);
                    }
                    $d->delete();
                }
            }

            $relacion->update([
                'fecha_inicio' => $fecha_inicial->format("Y-m-d H:i:s"),
                'fecha_fin' => $fecha_final->format("Y-m-d H:i:s"),
                'idempleado' => $data['id_empleado'],
                'idserie' => $data['id_serie'],
                'idmoneda' => $data['id_moneda'],
                'iddepartamento' => $data['id_departamento'],
                'idproyecto' => $data['id_proyecto'],
                'motivo' => $data['motivo']
            ]);
            DB::connection('controlrec')->commit();
            return $relacion;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function desvinculaFacturaRepositorio($uuid)
    {
        $factura_repositorio = FacturaRepositorio::where("uuid","=",$uuid)->first();
        if($factura_repositorio)
        {
            $factura_repositorio->id_doc_relacion_gastos_cr = NULL;
            $factura_repositorio->save();
        }
    }

    public function close($id)
    {
        return $this->repository->show($id)->cerrar();
    }

    public function open($id)
    {
        return $this->repository->show($id)->abrir();
    }

    public function pdfRelacion($id)
    {
        $pdf = new RelacionGastosFormato($this->repository->show($id));
        return $pdf->create();
    }
}
