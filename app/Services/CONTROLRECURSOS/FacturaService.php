<?php

namespace App\Services\CONTROLRECURSOS;

use App\Http\Requests\SatQueryRequest;
use App\Models\CONTROL_RECURSOS\CtgMoneda;
use App\Models\CONTROL_RECURSOS\Factura;
use App\Models\CONTROL_RECURSOS\Proveedor;
use App\Models\CONTROL_RECURSOS\Serie;
use App\Models\CONTROL_RECURSOS\TipoDocto;
use App\Models\IGH\TipoCambio;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Repositories\CONTROLRECURSOS\FacturaRepository as Repository;
use App\Services\SEGURIDAD_ERP\Contabilidad\CFDSATService;
use App\Utils\CFD;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FacturaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param Factura $model
     */
    public function __construct(Factura $model)
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

    public function cargaXML(array $data)
    {
        $archivo_xml = $data["factura"];
        $arreglo_cfd = $this->getArregloCFD($archivo_xml);
        $this->validaCFDI($arreglo_cfd['uuid']);
        return $arreglo_cfd;
    }

    private function getArregloCFD($archivo_xml)
    {
        $arreglo = [];
        $cfd = new CFD($archivo_xml);
        $arreglo_cfd = $cfd->getArregloFactura();
        $this->validaUUIDDocumento($arreglo_cfd["uuid"], $arreglo_cfd["tipo_comprobante"]);
        $arreglo["total"] = $arreglo_cfd["total"];
        $arreglo["impuesto"] = $arreglo_cfd["importe_iva"];
        $arreglo["tipo_comprobante"]  = $arreglo_cfd["tipo_comprobante"];
        $arreglo["otros"] = 0;
        if(array_key_exists('retenciones', $arreglo_cfd))
        {
            $arreglo["retencion"] = $arreglo_cfd['retenciones'][0]['importe'];
            if(count($arreglo_cfd['retenciones']) > 1) {
                $arreglo["otros"] = $arreglo_cfd['retenciones'][1]['importe'];
            }
        }else{
            $arreglo["retencion"] = 0;
        }
        $bandera = 0;
        if(array_key_exists('traslados', $arreglo_cfd))
        {
            $suma = 0;
            foreach ($arreglo_cfd['traslados'] as $traslado)
            {
                if($traslado['impuesto'] == '003')
                {
                    $suma = $suma + $traslado['importe'];
                }
                if($traslado['impuesto'] == '002')
                {
                    $bandera = 1;
                    $arreglo["impuesto"] = $arreglo_cfd['traslados'][0]['importe'];
                    if(count($arreglo_cfd['traslados']) > 1) {
                        $arreglo["otros"] = $arreglo_cfd['traslados'][1]['importe'];
                    }
                }
            }
            if($bandera != 1) {
                $arreglo["otros"] = $suma;
            }
        }else{
            $arreglo["otros"] = 0;
        }
        $arreglo["serie"] = $arreglo_cfd["serie"];
        $arreglo["folio"] = $arreglo_cfd["folio"] == "" ? substr($arreglo_cfd["uuid"],0,5) : $arreglo_cfd["folio"];
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
        if (!$arreglo["proveedor_bd"]) {
            abort(500, "El emisor (".$arreglo["emisor"]["rfc"].")del comprobante no esta dado de alta en el catálogo de proveedores de control recursos; la factura no puede ser registrada.");
        }

        if (!$arreglo["empresa_bd"]) {
            abort(500, "El receptor (".$arreglo["receptor"]["rfc"].") del comprobante no esta dado de alta en el catálogo de empresas de control recursos; la factura no puede ser registrada.");
        }
        $arreglo["moneda_bd"]["id_moneda"] = $this->repository->getMoneda($arreglo["moneda"]);
        $arreglo["id_moneda"] = $this->repository->getMoneda($arreglo["moneda"]);
        if($arreglo_cfd['tipo_cambio'] == '') {
            if($arreglo_cfd['moneda'] == 'MXN') {
                $arreglo['tipo_cambio'] = 1;
            }else{
                $tipo_cambio = TipoCambio::where('moneda', $arreglo["id_moneda"])->orderBy('fecha','desc')->first();
                $arreglo['tipo_cambio'] = $tipo_cambio->tipo_cambio;
            }
            $arreglo['tipo_cambio_excepcion'] = $arreglo['tipo_cambio'];
        }else{
            $arreglo['tipo_cambio'] = $arreglo_cfd['tipo_cambio'];
        }
        $arreglo["subtotal"] = $arreglo_cfd["subtotal"];
        $arreglo["descuento"] = $arreglo_cfd["descuento"];
        $arreglo["tasa_iva"] = $arreglo_cfd["tasa_iva"] * 100;
        $arreglo["uuid"] = $arreglo_cfd["uuid"];
        return $arreglo;
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

    public function store(array $data)
    {
        $this->validaCFDI($data['uuid']);
        $arreglo_cfd = $this->getArregloCFD($data["archivo"]);
        $this->validaExistenciaRepositorio($arreglo_cfd);
        $this->validaProveedor($arreglo_cfd, $data["id_proveedor"]);
        if($arreglo_cfd["version"] == 3.3 || $arreglo_cfd["version"] == 4.0){
            $this->validaCFDI33($data['archivo'], $arreglo_cfd);
        }

        $this->validaTotal($data["total"],$arreglo_cfd["total"],0);

        $vencimiento = New DateTime($data["vencimiento"]);
        $vencimiento->setTimezone(new DateTimeZone('America/Mexico_City'));

        $fecha = New DateTime($data["fecha"]);
        $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));

        $this->validaFechas($fecha, $vencimiento);
        try {
            DB::connection('controlrec')->beginTransaction();
            $factura = $this->repository->registrar($data);
            $factura_repositorio = $this->registrarCFDRepositorio($factura, $data);
            $this->registrarCFDSAT($factura_repositorio, $arreglo_cfd);
            $this->guardarXML($data);
            DB::connection('controlrec')->commit();
            return $factura;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    private function validaExistenciaRepositorio($arreglo_cfd)
    {
        $factura_repositorio = $this->repository->validaExistenciaRepositorio($arreglo_cfd["complemento"]["uuid"]);
        if($factura_repositorio){
            $factura_repositorio->load("usuario");
            abort(403, 'Comprobante utilizado previamente:
            Registró: ' . $factura_repositorio->usuario->nombre_completo . '
            BD: ' . $factura_repositorio->proyecto->base_datos . '
            Proyecto: ' . $factura_repositorio->obra . '
            Factura: ' . $factura_repositorio->factura->numero_folio . '
            Fecha Registro: '. $factura_repositorio->fecha_hora_registro_format . '
            UUID: ' . $arreglo_cfd["complemento"]["uuid"] . '
            Emisor: ' . $arreglo_cfd["emisor"]["nombre"] . '
            RFC Emisor: ' . $arreglo_cfd["emisor"]["rfc"] );
        }
    }

    private function validaProveedor($arreglo_cfd, $id_proveedor_seleccionado)
    {
        $proveedor_seleccionado = $this->repository->getBuscarProveedor($id_proveedor_seleccionado);
        if ((string) $arreglo_cfd["emisor"]["rfc"] != (string) str_replace('-','',$proveedor_seleccionado->RFC))
        {
            abort(500, "El proveedor seleccionado (" . $proveedor_seleccionado->RFC . ") no corresponde al RFC del emisor en el comprobante digital (" . $arreglo_cfd["emisor"]["rfc"] . ")");
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

    private function validaTotal($total, $total_cfd_factura, $total_cfd_nc =0)
    {
        if($total_cfd_nc == 0){
            if (abs($total_cfd_factura - $total_cfd_nc - $total) > 0.99) {
                abort(500, "El monto ingresado no corresponde al monto en el comprobante digital");
            }
        } else {
            if (abs($total_cfd_factura - $total_cfd_nc - $total) > 0.99) {
                abort(500, "El monto ingresado no corresponde al monto de los comprobantes digitales");
            }
        }
    }

    private function validaFechas($emision, $vencimiento)
    {
        if ($emision > $vencimiento) {
            abort(500, "La fecha de facturación no puede ser mayor a la fecha de vencimiento");
        }
    }

    private function guardarXML($datos)
    {
        $xml_split = explode('base64,', $datos['archivo']);
        $xml = base64_decode($xml_split[1]);
        Storage::disk('xml_control_recursos')->put($datos["uuid"] . ".xml", $xml);
    }

    public function validaCFDI($uuid)
    {
        if($uuid == 'UUID' || $uuid == '')
        {
            abort(500, "El documento no cuenta UUID, revisar con el proveedor.");
        }else {
            $factura = FacturaRepositorio::where('uuid', $uuid)->first();
            if ($factura) {
                if ($factura->id_transaccion != null) {
                    abort(500, "CFDI utilizado previamente:
                                Registró: " . $factura->usuario->nombre_completo . "
                                DB: " . $factura->proyecto->base_datos . "
                                Proyecto: " . $factura->obra . "
                                Tipo Transacción: " . $factura->transaccion->tipo_transaccion_str . "
                                Folio Transacción: " . $factura->transaccion->numero_folio . "
                                Fecha Registro: " . $factura->fecha_hora_registro_format . "
                                UUID: " . $uuid . "
                                Emisor: " . $factura->proveedor->razon_social . "
                                RFC Emisor: " . $factura->proveedor->rfc
                    );
                }
                if ($factura->id_documento_cr != null) {
                    $cr_factura = $this->repository->show($factura->id_documento_cr);
                    if ($cr_factura) {
                        abort(500, "CFDI utilizado previamente:
                                Registró: " . $factura->usuario->nombre_completo . "
                                Serie: " . $cr_factura->serie_descripcion . "
                                Folio: " . $cr_factura->FolioDocto . "
                                Fecha Registro: " . $cr_factura->fecha_format . "
                                UUID: " . $uuid . "
                                Emisor: " . $cr_factura->proveedor_descripcion . "
                                RFC Emisor: " . $cr_factura->rfc_proveedor);
                    }
                }
            }
        }
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function update(array $data, $id)
    {
        return $this->repository->show($id)->editar($data);
    }

    private function validaUUIDDocumento($uuid, $tipo)
    {
        $documento = $this->repository->buscarDocumentoUuid($uuid);
        $repositorio_factura = $this->repository->buscarRepositorioFactura($uuid);

        if ($documento && $repositorio_factura && $repositorio_factura->id_documento_cr != null)
        {
            abort(500, "CFDI utilizado previamente:
                                Registró: ".$documento->usuario->nombre_completo."
                                Serie: ".$documento->serie_descripcion."
                                Folio: ".$documento->FolioDocto."
                                Fecha Registro: ".$documento->fecha_format."
                                UUID: ".$uuid."
                                Emisor: ".$documento->proveedor_descripcion."
                                RFC Emisor: ".$documento->rfc_proveedor);
        }
        if ($repositorio_factura && $repositorio_factura->id_transaccion != null)
        {
            abort(500, "CFDI utilizado previamente:
                                Registró: ".$repositorio_factura->usuario->nombre_completo."
                                DB: ".$repositorio_factura->proyecto->base_datos."
                                Proyecto: ".$repositorio_factura->obra."
                                Tipo Transacción: ". $repositorio_factura->transaccion->tipo_transaccion_str."
                                Folio Transacción: ".$repositorio_factura->transaccion->numero_folio."
                                Fecha Registro: ".$repositorio_factura->fecha_hora_registro_format ."
                                UUID: ".$uuid."
                                Emisor: ".$repositorio_factura->proveedor->razon_social."
                                RFC Emisor: ".$repositorio_factura->proveedor->rfc);
        }
        if ($documento)
        {
            abort(500, "CFDI utilizado previamente:
                                Registró: ".$documento->usuario->nombre_completo."
                                Serie: ".$documento->serie_descripcion."
                                Folio: ".$documento->FolioDocto."
                                Fecha Registro: ".$documento->fecha_format."
                                UUID: ".$uuid."
                                Emisor: ".$documento->proveedor_descripcion."
                                RFC Emisor: ".$documento->rfc_proveedor);
        }
        if($tipo != 'I')
        {
            abort(500, 'El CFDI ingresado no es de un tipo válido, favor de subir CFDI’s tipo I (Ingreso) únicamente.');
        }
    }

    public function delete($data, $id)
    {
        return $this->repository->eliminar($id);
    }

    private function registrarCFDRepositorio($factura, $data)
    {
        $factura_repositorio = FacturaRepositorio::where("uuid","=",$data["uuid"])->first();
        if($factura_repositorio){
            if($factura_repositorio->id_transaccion == null && ($factura_repositorio->id_documento_cr == null || $factura_repositorio->documento_registrado == null))
            {
                $factura_repositorio->id_documento_cr = $factura->getKey();
                $factura_repositorio->save();
            }else{
                abort(500, "El CFDI fue utilizado anteriormente.");
            }

        }
        else{
            if($data){
                $factura_repositorio = FacturaRepositorio::create([
                    'xml_file' => $this->repository->getArchivoSQL($data['archivo']),
                    'hash_file' => hash_file('md5',$data['archivo']),
                    'uuid' => $data['complemento']['uuid'],
                    'rfc_emisor' => $data['emisor']['rfc'],
                    'rfc_receptor' => $data['receptor']['rfc'],
                    'tipo_comprobante' => $data['tipo_comprobante'],
                    'id_documento_cr' => $factura->getKey(),

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
}
