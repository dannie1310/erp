<?php

namespace App\Services\SEGURIDAD_ERP\Contabilidad;


use App\Jobs\ProcessCancelacionCFDI;

use App\Models\SEGURIDAD_ERP\Contabilidad\CFDISATNomina;

use App\Repositories\SEGURIDAD_ERP\Contabilidad\CFDISATNominaRepository;
use App\Utils\CFDINomina;
use DateTime;
use DateTimeZone;
use App\Utils\CFD;
use App\Utils\Util;
use App\Utils\Files;

use App\Events\CambioEFOS;
use Chumper\Zipper\Zipper;
use App\Events\FinalizaCargaCFD;
use App\Events\CambioNoLocalizados;

use Illuminate\Support\Facades\Storage;

use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT as Model;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\CFDSATRepository as Repository;

class CFDISATNominaService
{
    /**
     * @var Repository
     */
    protected $repository;
    protected $log;
    protected $carga;

    public function __construct(CFDISATNomina $model)
    {
        $this->repository = new CFDISATNominaRepository($model);
        $this->log["nombre_archivo_zip"] = "";
        $this->log["archivos_leidos"] = 0;
        $this->log["archivos_cargados"] = 0;
        $this->log["cfd_cargados"] = 0;
        $this->log["archivos_corruptos"] = 0;
        $this->log["archivos_tipo_incorrecto"] = 0;
        $this->log["archivos_no_cargados"] = 0;
        $this->log["cfd_no_cargados"] = 0;
        $this->log["archivos_preexistentes"] = 0;
        $this->log["cfd_preexistentes"] = 0;
        $this->log["archivos_receptor_no_valido"] = 0;
        $this->log["cfd_receptor_no_valido"] = 0;
        $this->log["receptores_no_validos"] = [];
        $this->log["proveedores_preexistentes"] = 0;
        $this->log["proveedores_nuevos"] = 0;
        $this->log["archivos_no_cargados_error_app"] = 0;
        $this->log["cfd_no_cargados_error_app"] = 0;
        $this->log["errores"] = [];
    }



    public function procesaDirectorioZIPCFDI()
    {

        ini_set('max_execution_time', '7200');
        $this->carga = $this->repository->iniciaCarga("inicial");

        $path = "uploads/contabilidad/zip_cfdi_nomina/";
        $this->preparaDirectorio($path);
        $this->procesaDirectorio($path);
        $this->log["fecha_hora_fin"] = date("Y-m-d H:i:s");
        $this->carga->update($this->log);

        if(file_exists(public_path("uploads/contabilidad/XML_errores/".$this->carga->id)))
        {
            $zipper = new Zipper;
            $zipper->make(public_path("uploads/contabilidad/XML_errores/".$this->carga->id.".zip"))->add(public_path("uploads/contabilidad/XML_errores/".$this->carga->id));
            $zipper->close();
        }
        $this->repository->finalizaCarga($this->carga);

        event(new FinalizaCargaCFD($this->carga));
        if(count($this->carga->cambios)>0){
            event(new CambioEFOS($this->carga->cambios));
        }

        $this->carga->load("usuario");
        $this->repository->actualizaNoLocalizados($this->carga);

        if(count($this->carga->noLocalizados)>0){
            event(new CambioNoLocalizados($this->carga->noLocalizados));
        }

        return $this->carga;
    }

    private function preparaDirectorio($path)
    {
        $dir = opendir(public_path($path));
        while ($current = readdir($dir)) {
            if ($current != "." && $current != "..") {
                if (is_dir($path . $current)) {
                    $this->preparaDirectorio($path . $current . '/');
                } else {
                    if (strpos($current, ".zip")) {
                        Files::extraeZIP($path . $current);
                    }
                }
            }
        }
        return true;
    }

    private function procesaDirectorio($path)
    {
        $dir = opendir($path);
        while ($current = readdir($dir)) {
            if ($current != "." && $current != "..") {
                if (is_dir($path . $current)) {
                    $this->procesaDirectorio($path . $current . "/");
                } else {
                    $this->log["archivos_leidos"] += 1;
                    $ruta_archivo = $path . "/" . $current;
                    if (strpos($current, ".xml")) {
                        $this->procesaArchivoCFDI($ruta_archivo, $current);
                    }
                    else {
                        $this->log["archivos_no_cargados"] += 1;
                        $this->log["cfd_no_cargados"] += 1;
                        $this->log["archivos_tipo_incorrecto"] += 1;
                        Storage::disk('xml_errores')->put($this->carga->id . '/tipo_incorrecto/' . $current, fopen($ruta_archivo, "r"));
                        unlink($ruta_archivo);
                    }
                }
            }
        }

        $contenido = @scandir($path);

        if (count($contenido) <= 2 && $path != "uploads/contabilidad/zip_cfdi_nomina/") {
            closedir($dir);
            rmdir($path);
        }
    }

    private function procesaArchivoCFDI($ruta_archivo, $current)
    {
        $contenido_archivo_xml = file_get_contents($ruta_archivo);
        $resultado = $this->setArregloFactura($ruta_archivo);
        if ($resultado == 0) {
            Storage::disk('xml_errores')->put($this->carga->id . '/error_app/' . $current, fopen($ruta_archivo, "r"));
            unlink($ruta_archivo);
        } else {
            if (key_exists("uuid", $this->arreglo_factura)) {
                if (!$this->repository->validaExistencia($resultado["uuid"])) {
                    if ($resultado["id_emisor"] > 0) {
                        $this->arreglo_factura["xml_file"] = $this->repository->getArchivoSQL(base64_encode($contenido_archivo_xml));
                        if ($this->store($resultado)) {
                            Storage::disk('xml_sat')->put($this->arreglo_factura["uuid"] . ".xml", fopen($ruta_archivo, "r"));
                            unlink($ruta_archivo);
                            $this->log["archivos_cargados"] += 1;
                            $this->log["cfd_cargados"] += 1;
                        }
                    } else {
                        $this->log["cfd_no_cargados"] += 1;
                        $this->log["archivos_no_cargados"] += 1;
                        $this->log["archivos_receptor_no_valido"] += 1;
                        $this->log["receptores_no_validos"][] = $resultado["id_emisor"];
                        Storage::disk('xml_errores')->put($this->carga->id . '/receptor_no_valido/' . $current, fopen($ruta_archivo, "r"));
                        unlink($ruta_archivo);
                    }
                } else {
                    $this->log["archivos_preexistentes"] += 1;
                    $this->log["archivos_no_cargados"] += 1;
                    $this->log["cfd_no_cargados"] += 1;
                    unlink($ruta_archivo);
                }
            } else {
                $this->log["cfd_no_cargados"] += 1;
                $this->log["archivos_no_cargados"] += 1;
                $this->log["archivos_corruptos"] += 1;
                Storage::disk('xml_errores')->put($this->carga->id . '/corruptos/' . $current, fopen($ruta_archivo, "r"));
                unlink($ruta_archivo);
            }
        }
    }

    private function setArregloFactura($archivo_xml)
    {

        $cfdi_nomina = new CFDINomina($archivo_xml);
        $arreglo_cfd = $cfdi_nomina->getArreglo();


        $this->arreglo_factura = $arreglo_cfd;
        $this->arreglo_factura["id_carga_cfd_sat"] = ($this->carga)?$this->carga->id:null;

        return 1;
    }

    private function store($arreglo_factura = null)
    {
        if($arreglo_factura){
            $transaccion_cfd = $this->repository->registrar($arreglo_factura);
        } else {
            $transaccion_cfd = $this->repository->registrar($this->arreglo_factura);
        }

        return $transaccion_cfd;
    }

    private function getFecha(string $fecha)
    {
        $fecha_xml = DateTime::createFromFormat('Y-m-d\TH:i:s', $fecha);
        if (!$fecha_xml) {
            $fecha_xml = DateTime::createFromFormat('Y-m-d\TH:i:s.u', $fecha);
            if (!$fecha_xml) {
                $fecha_xml = DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $fecha);
                if (!$fecha_xml) {
                    $fecha_xml = DateTime::createFromFormat('Y-m-d\TH:i:s', substr($fecha, 0, 19));
                    if (!$fecha_xml) {
                        $fecha_xml = substr($fecha, 0, 19);
                    }
                }
            }
        }
        return $fecha_xml;
    }

    private function getFechaHora(string $fecha)
    {
        $fecha_xml = DateTime::createFromFormat('Y-m-d\TH:i:s', $fecha);
        if (!$fecha_xml) {
            $fecha_xml = DateTime::createFromFormat('Y-m-d\TH:i:s.u', $fecha);
            if (!$fecha_xml) {
                $fecha_xml = substr($fecha, 0, 19);
            }
        }
        return $fecha_xml->format('Y-m-d H:i:s');
    }

    private function setArreglo33($factura_xml)
    {
        try {
            $this->arreglo_factura["descuento"] = (float)$factura_xml["Descuento"];
            $this->arreglo_factura["total"] = (float)$factura_xml["Total"];
            $this->arreglo_factura["subtotal"] = (float)$factura_xml["SubTotal"];
            $this->arreglo_factura["tipo_comprobante"] = strtoupper(substr((string)$factura_xml["TipoDeComprobante"], 0, 1));
            $this->arreglo_factura["serie"] = (string)$factura_xml["Serie"];
            $this->arreglo_factura["folio"] = (string)$factura_xml["Folio"];
            $this->arreglo_factura["fecha"] = $this->getFecha((string)$factura_xml["Fecha"]);
            $this->arreglo_factura["fecha_hora"] = $this->getFechaHora((string)$factura_xml["Fecha"]);
            $this->arreglo_factura["version"] = (string)$factura_xml["Version"];
            $this->arreglo_factura["moneda"] = (string)$factura_xml["Moneda"];
            $this->arreglo_factura["tipo_cambio"] = (string)$factura_xml["TipoCambio"];
            $this->arreglo_factura["metodo_pago"] = (string)$factura_xml["MetodoPago"];
            $this->arreglo_factura["no_certificado"] = (string)$factura_xml["NoCertificado"];
            $this->arreglo_factura["certificado"] = (string)$factura_xml["Certificado"];
            $emisor = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Emisor')[0];
            $this->arreglo_factura["emisor"]["rfc"] = (string)$emisor["Rfc"][0];
            $this->arreglo_factura["emisor"]["razon_social"] = (string)$emisor["Nombre"][0];
            $this->arreglo_factura["emisor"]["regimen_fiscal"] = (string)Util::eliminaCaracteresEspeciales($emisor["RegimenFiscal"][0]);
            $this->arreglo_factura["rfc_emisor"] = $this->arreglo_factura["emisor"]["rfc"];
            $receptor = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Receptor')[0];
            $this->arreglo_factura["receptor"]["rfc"] = (string)$receptor["Rfc"][0];
            $this->arreglo_factura["receptor"]["nombre"] = (string)$receptor["Nombre"][0];
            $this->arreglo_factura["rfc_receptor"] = $this->arreglo_factura["receptor"]["rfc"];
            $this->arreglo_factura["importe_iva"] = 0;
            $this->arreglo_factura["tasa_iva"] = 0;
            $this->arreglo_factura["traslados"] = [];
        } catch (\Exception $e) {
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }

        $this->arreglo_factura["tipo_relacion"] = '';
        $this->arreglo_factura["cfdi_relacionado"]  ='';

        $CFDIRelacionado = $factura_xml->xpath('//cfdi:Comprobante//cfdi:CfdiRelacionados');
        if(count($CFDIRelacionado)>0){
            $CFDIRelacionado = $factura_xml->xpath('//cfdi:Comprobante//cfdi:CfdiRelacionados')[0];
            $this->arreglo_factura["tipo_relacion"] = (string)$CFDIRelacionado["TipoRelacion"][0];
            $this->arreglo_factura["cfdi_relacionado"] = (string)$factura_xml->xpath('//cfdi:Comprobante//cfdi:CfdiRelacionados//cfdi:CfdiRelacionado')[0]["UUID"];
        }

        try {
            $ns = $factura_xml->getNamespaces(true);
            $impuestos = $factura_xml->xpath('//cfdi:Comprobante/cfdi:Impuestos');
            if (count($impuestos) >= 1) {
                $this->arreglo_factura["total_impuestos_trasladados"] = (float)$impuestos[0]["TotalImpuestosTrasladados"];
            } else {
                $this->arreglo_factura["total_impuestos_trasladados"] = (float)0;
            }
            $traslados = $factura_xml->xpath('//cfdi:Comprobante/cfdi:Impuestos//cfdi:Traslado');

            $i = 0;
            foreach ($traslados as $traslado) {
                if ($traslado["Impuesto"] == "002") {
                    $this->arreglo_factura["importe_iva"] = (float)$traslado["Importe"];
                    $this->arreglo_factura["tasa_iva"] = (float)$traslado["TasaOCuota"];
                }
                $this->arreglo_factura["traslados"][$i]["impuesto"] = (string)$traslado["Impuesto"];
                $this->arreglo_factura["traslados"][$i]["tipo_factor"] = (string)$traslado["TipoFactor"];
                $this->arreglo_factura["traslados"][$i]["tasa_o_cuota"] = (float)$traslado["TasaOCuota"];
                $this->arreglo_factura["traslados"][$i]["importe"] = (float)$traslado["Importe"];
                $this->arreglo_factura["traslados"][$i]["base"] =  (float)$traslado["Base"];
                $i++;
            }
            if (count($impuestos) >= 1) {
                $this->arreglo_factura["total_impuestos_retenidos"] = (float)$impuestos[0]["TotalImpuestosRetenidos"];
            } else {
                $this->arreglo_factura["total_impuestos_retenidos"] = (float)0;
            }

            $retenciones = $factura_xml->xpath('//cfdi:Comprobante/cfdi:Impuestos//cfdi:Retencion');

            $iret = 0;
            foreach ($retenciones as $retencion) {
                if ($retencion["Impuesto"] == "002") {
                    $this->arreglo_factura["importe_iva_retenido"] = (float)$retencion["Importe"];
                    $this->arreglo_factura["tasa_iva_retenido"] = (float)$retencion["TasaOCuota"];
                }
                $this->arreglo_factura["retenciones"][$iret]["impuesto"] = (string)$retencion["Impuesto"];
                $this->arreglo_factura["retenciones"][$iret]["tipo_factor"] = (string)$retencion["TipoFactor"];
                $this->arreglo_factura["retenciones"][$iret]["tasa_o_cuota"] = (float)$retencion["TasaOCuota"];
                $this->arreglo_factura["retenciones"][$iret]["importe"] = (float)$retencion["Importe"];
                $this->arreglo_factura["retenciones"][$iret]["base"] = (float)$retencion["Base"];
                $iret++;
            }

            $conceptos = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Concepto');
            $i = 0;
            $ic = 1;
            foreach ($conceptos as $concepto) {
                $this->arreglo_factura["conceptos"][$i]["clave_prod_serv"] = (string)$concepto["ClaveProdServ"];
                $this->arreglo_factura["conceptos"][$i]["no_identificacion"] = (string)$concepto["NoIdentificacion"];
                $this->arreglo_factura["conceptos"][$i]["cantidad"] = (float)$concepto["Cantidad"];
                $this->arreglo_factura["conceptos"][$i]["clave_unidad"] = (string)$concepto["ClaveUnidad"];
                $this->arreglo_factura["conceptos"][$i]["unidad"] = (string)$concepto["Unidad"];
                $this->arreglo_factura["conceptos"][$i]["descripcion"] = (string)$concepto["Descripcion"];
                $this->arreglo_factura["conceptos"][$i]["valor_unitario"] = (float)$concepto["ValorUnitario"];
                $this->arreglo_factura["conceptos"][$i]["importe"] = (float)$concepto["Importe"];
                $this->arreglo_factura["conceptos"][$i]["descuento"] = (float)$concepto["Descuento"];
                $traslados_concepto = $factura_xml->xpath("/cfdi:Comprobante/cfdi:Conceptos/cfdi:Concepto[".$ic."]/cfdi:Impuestos/cfdi:Traslados/cfdi:Traslado");
                $itc = 0;
                foreach ($traslados_concepto as $traslado_concepto) {
                    $this->arreglo_factura["conceptos"][$i]["traslados"][$itc]["base"] = (float)$traslado_concepto["Base"];
                    $this->arreglo_factura["conceptos"][$i]["traslados"][$itc]["impuesto"] = (string)$traslado_concepto["Impuesto"];
                    $this->arreglo_factura["conceptos"][$i]["traslados"][$itc]["importe"] = (float)$traslado_concepto["Importe"];
                    $this->arreglo_factura["conceptos"][$i]["traslados"][$itc]["tasa_o_cuota"] = (float)$traslado_concepto["TasaOCuota"];
                    $this->arreglo_factura["conceptos"][$i]["traslados"][$itc]["tipo_factor"] = (string)$traslado_concepto["TipoFactor"];
                    $itc++;
                }
                $retenciones_concepto = $factura_xml->xpath("/cfdi:Comprobante/cfdi:Conceptos/cfdi:Concepto[".$ic."]/cfdi:Impuestos/cfdi:Retenciones/cfdi:Retencion");
                $irc = 0;
                foreach ($retenciones_concepto as $retencion_concepto) {
                    $this->arreglo_factura["conceptos"][$i]["retenciones"][$irc]["base"] = (float)$retencion_concepto["Base"];
                    $this->arreglo_factura["conceptos"][$i]["retenciones"][$irc]["impuesto"] = (string)$retencion_concepto["Impuesto"];
                    $this->arreglo_factura["conceptos"][$i]["retenciones"][$irc]["importe"] = (float)$retencion_concepto["Importe"];
                    $this->arreglo_factura["conceptos"][$i]["retenciones"][$irc]["tasa_o_cuota"] = (float)$retencion_concepto["TasaOCuota"];
                    $this->arreglo_factura["conceptos"][$i]["retenciones"][$irc]["tipo_factor"] = (string)$retencion_concepto["TipoFactor"];
                    $irc++;
                }
                $i++;
                $ic++;
            }

        } catch (\Exception $e) {
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }

        try {
            if (key_exists("cfdi", $ns)) {
                $factura_xml->registerXPathNamespace('c', $ns['cfdi']);
            }
            $factura_xml->registerXPathNamespace('t', $ns['tfd']);
            $complemento = $factura_xml->xpath('//t:TimbreFiscalDigital')[0];
            $this->arreglo_factura["uuid"] = (string)$complemento["UUID"][0];
            if (!$this->arreglo_factura["folio"]) {
                try {
                    $factura_xml->registerXPathNamespace('rf', $ns['registrofiscal']);
                    $CFDI_RF = $factura_xml->xpath('//rf:CFDIRegistroFiscal')[0];
                    $this->arreglo_factura["folio"] = $CFDI_RF["Folio"];
                } catch (\Exception $e) {
                    $this->arreglo_factura["folio"] = "";
                }
            }
        } catch (\Exception $e) {
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }
        $this->arreglo_factura["id_empresa_sat"] = $this->repository->getIdEmpresa($this->arreglo_factura["receptor"]);
        $proveedor = $this->repository->getProveedorSAT($this->arreglo_factura["emisor"], $this->arreglo_factura["id_empresa_sat"]);
        $this->arreglo_factura["id_proveedor_sat"] = $proveedor["id_proveedor"];

        if ($proveedor["nuevo"] > 0) {
            $this->log["proveedores_nuevos"] += 1;
        }
        return 1;
    }

    private function setArreglo32($factura_xml)
    {
        $this->arreglo_factura["subtotal"] = (float)$factura_xml["subTotal"];
        $this->arreglo_factura["tipo_comprobante"] = strtoupper(substr((string)$factura_xml["tipoDeComprobante"], 0, 1));
        $this->arreglo_factura["descuento"] = (float)$factura_xml["descuento"];
        $this->arreglo_factura["total"] = (float)$factura_xml["total"];
        $this->arreglo_factura["serie"] = (string)$factura_xml["serie"];
        $this->arreglo_factura["folio"] = (string)$factura_xml["folio"];
        $this->arreglo_factura["moneda"] = (string)$factura_xml["Moneda"];
        $this->arreglo_factura["tipo_cambio"] = (float)$factura_xml["TipoCambio"];
        $this->arreglo_factura["fecha"] = $this->getFecha((string)$factura_xml["fecha"]);

        $ns = $factura_xml->getNamespaces(true);
        $factura_xml->registerXPathNamespace('t', $ns['tfd']);
        $complemento = $factura_xml->xpath('//t:TimbreFiscalDigital')[0];
        $uuid = (string)$complemento["UUID"][0];
        $this->arreglo_factura["uuid"] = $uuid;

        try {
            $emisor_arr = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Emisor');
            if ($emisor_arr) {
                if (key_exists(0, $emisor_arr)) {
                    $emisor = $emisor_arr[0];
                    $this->arreglo_factura["emisor"]["regimen_fiscal"] = (string) Util::eliminaCaracteresEspeciales($factura_xml->xpath('//cfdi:Comprobante//cfdi:Emisor//cfdi:RegimenFiscal')[0]["Regimen"]);
                } else {
                    $emisor = $factura_xml->Emisor;
                    $this->arreglo_factura["emisor"]["regimen_fiscal"] = (string)Util::eliminaCaracteresEspeciales($emisor->RegimenFiscal[0]["Regimen"]);
                }
            } else {
                $emisor = $factura_xml->Emisor;
                $this->arreglo_factura["emisor"]["regimen_fiscal"] = (string)Util::eliminaCaracteresEspeciales($emisor->RegimenFiscal[0]["Regimen"]);
            }

            $this->arreglo_factura["emisor"]["rfc"] = (string)$emisor["rfc"][0];
            $this->arreglo_factura["rfc_emisor"] = $this->arreglo_factura["emisor"]["rfc"];
            $this->arreglo_factura["emisor"]["razon_social"] = (string)$emisor["nombre"][0];
        } catch (\Exception $e) {
            //abort(500, "Hubo un error al leer el emisor del comprobante: ".$uuid." mensaje:" . $e->getMessage());
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }

        try {
            $receptor_arr = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Receptor');
            if ($receptor_arr) {
                if (key_exists(0, $receptor_arr)) {
                    $receptor = $receptor_arr[0];
                } else {
                    $receptor = $factura_xml->Receptor;
                }
            } else {
                $receptor = $factura_xml->Receptor;
            }

            $this->arreglo_factura["receptor"]["rfc"] = (string)$receptor["rfc"][0];
            $this->arreglo_factura["rfc_receptor"] = $this->arreglo_factura["receptor"]["rfc"];
            $this->arreglo_factura["receptor"]["nombre"] = (string)$receptor["nombre"][0];
        } catch (\Exception $e) {
            //abort(500, "Hubo un error al leer el receptor del comprobante: ".$uuid." mensaje:" . $e->getMessage());
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }

        try {
            $impuestos = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Impuestos');
            $this->arreglo_factura["total_impuestos_trasladados"] = (float)$impuestos[0]["totalImpuestosTrasladados"][0];
            $traslados = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Traslado');
            $i = 0;
            foreach ($traslados as $traslado) {
                if ($traslado["impuesto"] == "IVA") {
                    $this->arreglo_factura["importe_iva"] = (float)$traslado["importe"];
                    $this->arreglo_factura["tasa_iva"] = (float)$traslado["tasa"];
                }
                $this->arreglo_factura["traslados"][$i]["impuesto"] = (string)$traslado["impuesto"];
                $this->arreglo_factura["traslados"][$i]["tasa_o_cuota"] = (string)$traslado["tasa"];
                $this->arreglo_factura["traslados"][$i]["importe"] = (string)$traslado["importe"];
                $i++;
            }
            $conceptos = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Concepto');
            $i = 0;
            foreach ($conceptos as $concepto) {
                $this->arreglo_factura["conceptos"][$i]["cantidad"] = (float)$concepto["cantidad"];
                $this->arreglo_factura["conceptos"][$i]["descripcion"] = (string)$concepto["descripcion"];
                $this->arreglo_factura["conceptos"][$i]["importe"] = (float)$concepto["importe"];
                $this->arreglo_factura["conceptos"][$i]["no_identificacion"] = (string)$concepto["noIdentificacion"];
                $this->arreglo_factura["conceptos"][$i]["unidad"] = (string)$concepto["unidad"];
                $this->arreglo_factura["conceptos"][$i]["valor_unitario"] = (float)$concepto["valorUnitario"];
                $i++;
            }
        } catch (\Exception $e) {
            //abort(500, "Hubo un error al leer la ruta de impuestos o conceptos: " . $e->getMessage());
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }
        try {
            if (key_exists("cfdi", $ns)) {
                $factura_xml->registerXPathNamespace('c', $ns['cfdi']);
            }

            if (!$this->arreglo_factura["folio"]) {
                try {
                    $factura_xml->registerXPathNamespace('rf', $ns['registrofiscal']);
                    $CFDI_RF = $factura_xml->xpath('//rf:CFDIRegistroFiscal')[0];
                    $this->arreglo_factura["folio"] = $CFDI_RF["folio"];
                } catch (\Exception $e) {
                    $this->arreglo_factura["folio"] = "";
                }
            }
        } catch (\Exception $e) {
            //abort(500, "Hubo un error al leer la ruta de complemento: " . $e->getMessage());
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }
        $this->arreglo_factura["id_empresa_sat"] = $this->repository->getIdEmpresa($this->arreglo_factura["receptor"]);
        $proveedor = $this->repository->getProveedorSAT($this->arreglo_factura["emisor"], $this->arreglo_factura["id_empresa_sat"]);
        $this->arreglo_factura["id_proveedor_sat"] = $proveedor["id_proveedor"];
        if ($proveedor["nuevo"] > 0) {
            $this->log["proveedores_nuevos"] += 1;
        }
        return 1;
    }


    private function validaEmisor($arreglo_cfd)
    {
        $rfc_receptoras = $this->repository->getRFCReceptoras();
        if (!in_array( $arreglo_cfd["receptor"]["rfc"], $rfc_receptoras)) {
            abort(500, "El RFC del receptor en el comprobante digital (" . $arreglo_cfd["receptor"]["rfc"] . ") no esta dado de alta en los registros de Hermes Infraestructura.");
        }
    }

    public function obtenerListaCFDI($data)
    {

        $fecha = New DateTime($data["fecha_inicial"]);
        $fecha_final = New DateTime($data["fecha_final"]);

        if(!($fecha_final>$fecha)){
            $fecha = New DateTime($data["fecha_final"]);
            $fecha_final = New DateTime($data["fecha_inicial"]);
        }

        $data["fecha_inicial"]=$fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data["fecha_final"]=$fecha_final->setTimezone(new DateTimeZone('America/Mexico_City'));

        $cfdiRepository = new Repository(new Model());

        return $cfdiRepository->getListaCFDI($data);

    }

    public function obtenerListaCFDIMesAnio($data)
    {
        $cfdiRepository = new Repository(new Model());
        return $cfdiRepository->obtenerListaCFDIMesAnio($data);
    }

    public function obtenerListaCFDICostosCFDIBalanza($data)
    {
        $cfdiRepository = new Repository(new Model());
        return $cfdiRepository->obtenerListaCFDICostosCFDIBalanza($data);
    }

    public function detectarCancelaciones()
    {
        ini_set('max_execution_time', '7200');
        ini_set('memory_limit', -1);

        $hoy_str = date('Y-m-d');
        $hace_1Y_str = date("Y-m-d",strtotime($hoy_str."- 1 years"));
        $hace_1Y = DateTime::createFromFormat('Y-m-d', $hace_1Y_str);

        $cantidad = Model::where("cancelado","=","0")
            /*->whereIn("tipo_comprobante",["I","E"])*/
            ->whereBetween("fecha",[$hace_1Y->format("Y-m-") . "01 00:00:00",$hoy_str." 23:59:59"])
            ->count();

        $take = 1000;

        for ($i = 0; $i <= ($cantidad + 1000); $i += $take) {
            $cfd = Model::where("cancelado","=","0")
                /*->whereIn("tipo_comprobante",["I","E"])*/
                ->whereBetween("fecha",[$hace_1Y->format("Y-m-") . "01 00:00:00",$hoy_str." 23:59:59"])
                ->skip($i)
                ->take($take)
                ->orderBy("id","asc")
                ->get();

            $idistribucion = 0;
            foreach ($cfd as $rcfd) {
                ProcessCancelacionCFDI::dispatch($rcfd)->onQueue("q".$idistribucion);
                //$rcfd->validaVigencia();
                $idistribucion ++;
                if($idistribucion==5){
                    $idistribucion=0;
                }
            }
        }
    }
}
