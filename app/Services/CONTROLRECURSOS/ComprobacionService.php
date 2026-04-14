<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\Comprobacion;
use App\Models\CONTROL_RECURSOS\CtgMoneda;
use App\Models\CONTROL_RECURSOS\DoctoComp;
use App\Models\CONTROL_RECURSOS\Empresa;
use App\Models\CONTROL_RECURSOS\Proveedor;
use App\Models\CONTROL_RECURSOS\Serie;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\PDF\ControlRecursos\ComprobacionGastosFormato;
use App\Repositories\CONTROLRECURSOS\ComprobacionRepository as Repository;
use App\Services\SEGURIDAD_ERP\Contabilidad\CFDSATService;
use App\Utils\CFD;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ComprobacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param Comprobacion $model
     */
    public function __construct(Comprobacion $model)
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

        if (isset($data['idproveedor']))
        {
            $proveedor = Proveedor::where([['RazonSocial', 'LIKE', '%' . $data['idproveedor'] . '%']])->empleados()->pluck('IdProveedor');
            $this->repository->whereIn(['IdProveedor', $proveedor]);
        }

        if (isset($data['idempresa']))
        {
            $empresa = Empresa::where([['RazonSocial', 'LIKE', '%' . $data['idempresa'] . '%']])->pluck('IdEmpresa');
            $this->repository->whereIn(['IdEmpresa', $empresa]);
        }

        if (isset($data['Fecha']))
        {
            $this->repository->whereBetween( ['Fecha', [ request( 'Fecha' )." 00:00:00",request( 'Fecha' )." 23:59:59"]] );
        }

        if (isset($data['folio']))
        {
            $folio = explode("-", $data['folio']);
            if(count($folio) == 2)
            {
                $this->repository->where([['Serie', '=', $folio[0]]])->where([['Folio', 'LIKE', '%'.$folio[1].'%']]);

            }else {
                $this->repository->where([['Folio', 'LIKE', '%' . $folio[0] . '%']]);
            }
        }

        if (isset($data['importe']))
        {
            $this->repository->where([['Importe', 'LIKE', '%'.$data['total'].'%']]);
        }

        if (isset($data['alias_depto']))
        {
            $this->repository->where([['Alias_Depto', 'LIKE', '%'.$data['alias_depto'].'%']]);
        }

        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store(array $data)
    {
        $fecha_inicial = new DateTime($data["fecha_inicial"]);
        $fecha_inicial->setTimezone(new DateTimeZone('America/Mexico_City'));

        $fecha_final = new DateTime($data["fecha_final"]);
        $fecha_final->setTimezone(new DateTimeZone('America/Mexico_City'));

        $data['fecha_inicial'] = $fecha_inicial->format("Y-m-d H:i:s");
        $data['fecha_final'] = $fecha_final->format("Y-m-d H:i:s");

        return $this->repository->registrar($data);
    }

    public function update(array $data, $id)
    {
        $fecha_inicial = new DateTime($data['del_editar']);
        $fecha_inicial->setTimezone(new DateTimeZone('America/Mexico_City'));
        $fecha_final = new DateTime($data['al_editar']);
        $fecha_final->setTimezone(new DateTimeZone('America/Mexico_City'));

        try {
            DB::connection('controlrec')->beginTransaction();
            DB::connection('seguridad')->beginTransaction();

            $comprobacion = $this->repository->show($id);
            $documentos = $comprobacion->documentos->pluck('IdDoctoComp')->toArray();

            foreach ($data['documentos'] as $partida) {
                if(array_key_exists('fecha_editar', $partida)) {
                    $fecha = new DateTime($partida['fecha_editar']);
                }else{
                    $fecha = new DateTime($partida['fecha']);
                }
                $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
                $partida['fecha'] = $fecha->format("Y-m-d");

                if(array_key_exists('id', $partida)) {
                    $documento = DoctoComp::find($partida['id']);

                    $documento->update([
                        'FechaDocto' => $partida['fecha'],
                        'TipoDoctoComp' => $partida['tipo_docto_comp'],
                        'FolioDocto' => $partida['folio_docto'],
                        'Concepto' => $partida['tipo_gasto'],
                        'Importe' => $partida['importe'],
                        'IVA' => $partida['iva'],
                        'Total' => $partida['total'],
                        'Observaciones' => $partida['observaciones'],
                        'retencion' => $partida['retencion']
                    ]);

                    $key = array_search($partida['id'], $documentos, true);
                    if ($key !== false) {
                        unset($documentos[$key]);
                    }
                }
                else {
                    if (array_key_exists('uuid', $partida) && $partida['uuid'] != null)
                    {
                        $arreglo_cfd = $this->getArregloCFD($partida['xml']);
                        $this->validaCFDI($partida['uuid']);
                        $this->validaReceptor($this->repository->getBuscarEmpresa($comprobacion->EmpresaFacturar), $arreglo_cfd);
                        $documento = $comprobacion->registrarDocumento($partida);
                        $factura_repositorio = $this->registrarCFDRepositorio($documento, $arreglo_cfd, $partida['xml']);
                        $this->registrarCFDSAT($factura_repositorio, $arreglo_cfd);
                        $this->guardarXML($partida);
                    }else {
                        $comprobacion->registrarDocumento($partida);
                    }
                }
            }

            if(count($documentos) > 0)
            {
                foreach ($documentos as $documento) {
                    $d = DoctoComp::find($documento);
                    if($d->uuid != null)
                    {
                        $this->desvinculaFacturaRepositorio($d->uuid);
                    }
                    $d->delete();
                }
            }

            $comprobacion->actualizaSaldoPendienteSolicitud();

            $comprobacion->update([
                'Del' => $fecha_inicial->format("Y-m-d H:i:s"),
                'Al' => $fecha_final->format("Y-m-d H:i:s"),
                'Motivo' => $data['motivo']
            ]);
            DB::connection('controlrec')->commit();
            DB::connection('seguridad')->commit();
            return $comprobacion;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
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
        /**
        if (!$arreglo["empresa_bd"]) {
        abort(500, "El receptor (".$arreglo["receptor"]["rfc"].") del comprobante no esta dado de alta en el catálogo de empresas de control recursos; la factura no puede ser registrada.");
        }
         **/

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
            $comprobacion = $this->repository->buscarDocumentoUuid($uuid);
            $factura = $this->repository->buscarPagoDocumentoUuid($uuid);
            $relacion = $this->repository->buscarRelacionDocumentoUuid($uuid);
            if ($comprobacion && $repositorio_factura->id_doc_comprobacion_gastos_cr != null) {
                abort(500, "CFDI utilizado previamente:
                                Registró: " . $repositorio_factura->usuario->nombre_completo . "
                                Folio: " . $comprobacion->comprobacion->folio_format . "
                                Fecha Registro: " . $comprobacion->fecha_format . "
                                UUID: " . $uuid . "
                                Emisor: " . $comprobacion->comprobacion->proveedor_descripcion . "
                                RFC Emisor: " .  $comprobacion->comprobacion->rfc_proveedor);
            }

            if ($relacion && $repositorio_factura->id_doc_relacion_gastos_cr != null) {
                abort(500, "CFDI utilizado previamente:
                                Registró: " . $repositorio_factura->usuario->nombre_completo . "
                                Serie: " . $relacion->relacion_sin_global_scopes->serie_descripcion . "
                                Folio: " . $relacion->relacion_sin_global_scopes->numero_folio . "
                                Fecha Registro: " . $relacion->fecha_format . "
                                UUID: " . $uuid . "
                                Emisor: " . $relacion->relacion_sin_global_scopes->empleado_descripcion . "
                                RFC Emisor: " . $relacion->relacion_sin_global_scopes->rfc_empleado);
            }

            if ($factura && $repositorio_factura->id_documento_cr != null) {
                abort(500, "CFDI utilizado previamente:
                                Registró: " . $repositorio_factura->usuario->nombre_completo . "
                                Serie: " . $factura->serie_descripcion . "
                                Folio: " . $factura->FolioDocto . "
                                Fecha Registro: " . $factura->fecha_format . "
                                UUID: " . $uuid . "
                                Emisor: " . $factura->proveedor_descripcion . "
                                RFC Emisor: " . $factura->rfc_proveedor);
            }
            if ($repositorio_factura && $repositorio_factura->id_transaccion != null) {
                abort(500, "CFDI utilizado previamente:
                                Registró: " . $repositorio_factura->usuario->nombre_completo . "
                                DB: " . $repositorio_factura->proyecto->base_datos . "
                                Proyecto: " . $repositorio_factura->obra . "
                                Tipo Transacción: " . ($repositorio_factura->transaccion != null ? $repositorio_factura->transaccion->tipo_transaccion_str : "No se encontro..."). "
                                Folio Transacción: " . ($repositorio_factura->transaccion != null ? $repositorio_factura->transaccion->numero_folio : "No se encontro...") . "
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

    public function validaCFDI($uuid)
    {
        $cfdi = CFDSAT::where('uuid', $uuid)->first();
        $factura = FacturaRepositorio::where('uuid', $uuid)->first();
        if ($factura && $cfdi) {
            if ($factura->id_transaccion != null || $factura->id_documento_cr != null || $factura->id_doc_relacion_gastos_cr != null || $factura->id_doc_comprobacion_gastos_cr != null) {
                abort(500, "El CFDI " . $uuid . " fue utilizado anteriormente.");
            }
        }
    }

    private function registrarCFDRepositorio($documento, $data, $xml)
    {
        $factura_repositorio = FacturaRepositorio::where("uuid","=",$data["uuid"])->first();
        if($factura_repositorio){
            $factura_repositorio->update([
                'id_doc_comprobacion_gastos_cr' => $documento->IdDoctoComp
            ]);
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
                    'id_doc_comprobacion_gastos_cr' => $documento->IdDoctoComp,

                ]);
                if (!$factura_repositorio) {
                    abort(400, "Hubo un error al registrar el CFDI en el repositorio");
                }
            }
        }
        return $factura_repositorio;
    }

    private function validaReceptor($empresa, $arreglo)
    {
        if($empresa->RFC != $arreglo['receptor']['rfc'])
        {
            abort(500, "El receptor (".$arreglo["receptor"]["rfc"].") del comprobante no es la misma empresa (".$empresa->RFC.") seleccionada para esta comprobación de gastos; la factura no puede ser registrada.");
        }
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
        Storage::disk('xml_control_recursos_comprobacion_gastos')->put($datos["uuid"] . ".xml", $xml);
    }

    public function desvinculaFacturaRepositorio($uuid)
    {
        $factura_repositorio = FacturaRepositorio::where("uuid","=",$uuid)->first();
        if($factura_repositorio)
        {
            $factura_repositorio->id_doc_comprobacion_gastos_cr = NULL;
            $factura_repositorio->save();
        }
    }

    public function delete($data, $id)
    {
        return $this->repository->show($id)->eliminar($data["data"]);
    }

    public function close($id)
    {
        return $this->repository->show($id)->cerrar();
    }

    public function open($id)
    {
        return $this->repository->show($id)->abrir();
    }

    public function pdf($id)
    {
        $pdf = new ComprobacionGastosFormato($this->repository->show($id));
        return $pdf->create();
    }
}
