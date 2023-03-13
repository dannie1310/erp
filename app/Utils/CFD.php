<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/07/2020
 * Time: 05:32 PM
 */

namespace App\Utils;
use App\Events\IncidenciaCI;
use App\Facades\Context;
use App\Http\Requests\SatQueryRequest;
use App\Models\CADECO\Obra;
use App\Models\CTPQ\Parametro;
use App\Models\SEGURIDAD_ERP\Finanzas\AvisoSATOmitir;
use DateTime;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Webpatser\Uuid\Uuid;

class CFD
{
    protected $arreglo_factura;
    protected $log;
    protected $archivo_xml;
    protected $logs;

    public function __construct($archivo_xml)
    {
        $this->archivo_xml = $archivo_xml;
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
        $this->getArregloFactura();
    }

    public function getArregloFactura()
    {
        $this->arreglo_factura = [];
        $this->arreglo_factura["xml"] = $this->archivo_xml;
        try {
            libxml_use_internal_errors(true);
            $factura_xml = simplexml_load_file($this->archivo_xml);
            if($factura_xml === false)
            {
                $factura_xml = simplexml_load_string($this->archivo_xml);
            }

            if(!$factura_xml){
                $errors = libxml_get_errors();
            }
        } catch (\Exception $e) {
            //abort(500, "Hubo un error al leer el archivo XML proporcionado. " . ' Ln.' . $e->getLine() . ' ' . $e->getMessage());
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }
        //$factura_simple_xml = new \SimpleXMLElement(file_get_contents($archivo_xml));
        if ((string)$factura_xml["version"] == "3.2") {
            $this->arreglo_factura["version"] = (string)$factura_xml["version"];
            $this->setArreglo32($factura_xml);
        } else if ($factura_xml["Version"] == "3.3" || $factura_xml["Version"] == "4.0") {
            $this->arreglo_factura["version"] = (string)$factura_xml["Version"];
            $this->setArreglo33($factura_xml);
        }
        return $this->arreglo_factura;
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
            $this->arreglo_factura["sello"] = (string)$factura_xml["Sello"];
            $emisor = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Emisor')[0];
            $this->arreglo_factura["emisor"]["rfc"] = (string)$emisor["Rfc"][0];
            $this->arreglo_factura["emisor"]["razon_social"] = (string)$emisor["Nombre"][0];
            $this->arreglo_factura["emisor"]["nombre"] = (string)$emisor["Nombre"][0];
            $this->arreglo_factura["emisor"]["regimen_fiscal"] = (string)$emisor["RegimenFiscal"][0];
            $this->arreglo_factura["rfc_emisor"] = $this->arreglo_factura["emisor"]["rfc"];
            $receptor = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Receptor')[0];
            $this->arreglo_factura["receptor"]["rfc"] = (string)$receptor["Rfc"][0];
            $this->arreglo_factura["receptor"]["nombre"] = (string)$receptor["Nombre"][0];
            $this->arreglo_factura["receptor"]["razon_social"] = (string)$receptor["Nombre"][0];
            $this->arreglo_factura["rfc_receptor"] = $this->arreglo_factura["receptor"]["rfc"];
            $this->arreglo_factura["importe_iva"] = 0;
            $this->arreglo_factura["tasa_iva"] = 0;
            $this->arreglo_factura["traslados"] = [];
        } catch (\Exception $e) {
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }

        if($this->arreglo_factura["tipo_comprobante"] == "P"){
            $this->setDatosPago($factura_xml);
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
                $this->arreglo_factura["traslados"][$i]["base"] = (float)$traslado["Base"];
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
            $this->arreglo_factura["complemento"]["uuid"] = (string)$complemento["UUID"][0];
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

        $complemento = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Complemento//implocal:RetencionesLocales');
        if($complemento != false) {
            foreach ($complemento as $key => $c) {
                $this->arreglo_factura["retencionesLocales"][$key]['descripcion'] = (string)$c['ImpLocRetenido'];
                $this->arreglo_factura["retencionesLocales"][$key]['total'] = (float)$c['Importe'];
                $this->arreglo_factura["retencionesLocales"][$key]['tasaRetencion'] = (float)$c['TasadeRetencion'];
            }
        }
    }

    public function setDatosPago($factura_xml)
    {
        $ns = $factura_xml->getNamespaces(true);
        if(key_exists("pago10",$ns))
        {
            $factura_xml->registerXPathNamespace('p', $ns['pago10']);

        }else{
            $factura_xml->registerXPathNamespace('p', $ns['pago20']);
        }
        $pagos = $factura_xml->xpath('//p:Pago');
        $doctos = $factura_xml->xpath('//p:Pago//p:DoctoRelacionado');

        $monto = 0 ;
        if($pagos){
            foreach($pagos as $pago)
            {
                $monto += (float) $pago["Monto"];
                $moneda = (string) $pago["MonedaP"];
                $forma_pago = (int) $pago["FormaDePagoP"];
                $fecha_pago = $this->getFecha((string)$pago["FechaPago"]);
            }

            $this->arreglo_factura["total"] = $monto;
            $this->arreglo_factura["moneda"] = $moneda;
            $this->arreglo_factura["forma_pago"] = $forma_pago;
            $this->arreglo_factura["forma_pago_p"] = $forma_pago;
            $this->arreglo_factura["fecha_pago"] = $fecha_pago;
            $this->arreglo_factura["moneda_pago"] = $moneda;
            $this->arreglo_factura["monto_pago"] = (float) $pago["Monto"];
        }

        if($doctos){
            $id = 0;
            foreach($doctos as $docto)
            {
                $this->arreglo_factura["documentos_pagados"][$id]["uuid"] = (string)$docto["IdDocumento"];
                $this->arreglo_factura["documentos_pagados"][$id]["moneda"] = (string)$docto["MonedaDR"];
                $this->arreglo_factura["documentos_pagados"][$id]["imp_saldo_insoluto"] = (float)$docto["ImpSaldoInsoluto"];
                $this->arreglo_factura["documentos_pagados"][$id]["imp_pagado"] = (float)$docto["ImpPagado"];
                $this->arreglo_factura["documentos_pagados"][$id]["imp_saldo_ant"] = (float)$docto["ImpSaldoAnt"];
                $this->arreglo_factura["documentos_pagados"][$id]["num_parcialidad"] = (int)$docto["NumParcialidad"];
                $this->arreglo_factura["documentos_pagados"][$id]["metodo_pago"] = (string)$docto["MetodoDePagoDR"];
                $id++;
            }
        }

        /*$pagos = $factura_xml->xpath('//cfdi:Comprobante//pago10:Pago');
        $ip = 0;
        $ipd = 1;
        foreach($pagos as $pago)
        {
            $this->arreglo_factura["pagos"][$ip]["fecha_pago"] = $this->getFechaHora((string)$pago["FechaPago"]);
            $this->arreglo_factura["pagos"][$ip]["forma_pago_p"] = (int) $pago["FormaDePagoP"];
            $this->arreglo_factura["pagos"][$ip]["moneda_pago"] = (string) $pago["MonedaP"];
            $this->arreglo_factura["pagos"][$ip]["monto_pago"] = (float) $pago["Monto"];

            $documentos_pagados = $factura_xml->xpath("/cfdi:Comprobante//pago10:Pagos/pago10:Pago[".$ipd."]//pago10:DoctoRelacionado");

            $dp = 0;

            foreach($documentos_pagados as $documento_pagado)
            {
                $this->arreglo_factura["pagos"][$ip]["documentos_pagados"][$dp]["uuid"] = (string)$documento_pagado["IdDocumento"];
                $this->arreglo_factura["pagos"][$ip]["documentos_pagados"][$dp]["moneda_dr"] = (string)$documento_pagado["MonedaDR"];
                $this->arreglo_factura["pagos"][$ip]["documentos_pagados"][$dp]["metodo_pago_dr"] = (string)$documento_pagado["MetodoDePagoDR"];
                $this->arreglo_factura["pagos"][$ip]["documentos_pagados"][$dp]["num_parcialidad"] = (int)$documento_pagado["NumParcialidad"];
                $this->arreglo_factura["pagos"][$ip]["documentos_pagados"][$dp]["imp_saldo_ant"] = (float)$documento_pagado["ImpSaldoAnt"];
                $this->arreglo_factura["pagos"][$ip]["documentos_pagados"][$dp]["imp_pagado"] = (float)$documento_pagado["ImpPagado"];
                $this->arreglo_factura["pagos"][$ip]["documentos_pagados"][$dp]["imp_saldo_insoluto"] = (float)$documento_pagado["ImpSaldoInsoluto"];

                $dp ++;
            }

            $ip ++;
            $ipd ++;
        }*/
    }

    private function setArreglo32($factura_xml)
    {
        $this->arreglo_factura["subtotal"] = (float)$factura_xml["subTotal"];
        $this->arreglo_factura["tipo_comprobante"] = strtoupper(substr((string)$factura_xml["tipoDeComprobante"], 0, 1));
        $this->arreglo_factura["descuento"] = (float)$factura_xml["descuento"];
        $this->arreglo_factura["total"] = (float)$factura_xml["total"];
        $this->arreglo_factura["serie"] = (string)$factura_xml["serie"];
        $this->arreglo_factura["folio"] = (string)$factura_xml["folio"];

        $this->arreglo_factura["fecha"] = $this->getFecha((string)$factura_xml["fecha"]);
        $this->arreglo_factura["fecha_hora"] = $this->getFechaHora((string)$factura_xml["fecha"]);
        $this->arreglo_factura["moneda"] = (string)$factura_xml["Moneda"];
        $this->arreglo_factura["tipo_cambio"] = (string)$factura_xml["TipoCambio"];
        $this->arreglo_factura["no_certificado"] = (string)$factura_xml["NoCertificado"];
        $this->arreglo_factura["certificado"] = (string)$factura_xml["Certificado"];

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
                    $this->arreglo_factura["emisor"]["regimen_fiscal"] = (string)$factura_xml->xpath('//cfdi:Comprobante//cfdi:Emisor//cfdi:RegimenFiscal')[0]["Regimen"];
                } else {
                    $emisor = $factura_xml->Emisor;
                    $this->arreglo_factura["emisor"]["regimen_fiscal"] = (string)$emisor->RegimenFiscal[0]["Regimen"];
                }
            } else {
                $emisor = $factura_xml->Emisor;
                $this->arreglo_factura["emisor"]["regimen_fiscal"] = (string)$emisor->RegimenFiscal[0]["Regimen"];
            }

            $this->arreglo_factura["emisor"]["rfc"] = (string)$emisor["rfc"][0];
            $this->arreglo_factura["rfc_emisor"] = $this->arreglo_factura["emisor"]["rfc"];
            $this->arreglo_factura["emisor"]["razon_social"] = (string)$emisor["nombre"][0];
            $this->arreglo_factura["emisor"]["nombre"] = (string)$emisor["nombre"][0];
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
            $this->arreglo_factura["receptor"]["razon_social"] = (string)$receptor["nombre"][0];
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

            $this->arreglo_factura["total_impuestos_retenidos"] = (float)$impuestos[0]["totalImpuestosRetenidos"][0];

            $conceptos = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Concepto');
            $i = 0;
            foreach ($conceptos as $concepto) {
                $this->arreglo_factura["conceptos"][$i]["clave_prod_serv"] = (string)$concepto["ClaveProdServ"];
                $this->arreglo_factura["conceptos"][$i]["cantidad"] = (float)$concepto["cantidad"];
                $this->arreglo_factura["conceptos"][$i]["descripcion"] = (string)$concepto["descripcion"];
                $this->arreglo_factura["conceptos"][$i]["importe"] = (float)$concepto["importe"];
                $this->arreglo_factura["conceptos"][$i]["no_identificacion"] = (string)$concepto["noIdentificacion"];
                $this->arreglo_factura["conceptos"][$i]["unidad"] = (string)$concepto["unidad"];
                $this->arreglo_factura["conceptos"][$i]["valor_unitario"] = (float)$concepto["valorUnitario"];
                if(key_exists("descuento", $concepto)){
                    $this->arreglo_factura["conceptos"][$i]["descuento"] = (float)$concepto["descuento"];
                } else {
                    $this->arreglo_factura["conceptos"][$i]["descuento"] = 0;
                }
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
        /*$this->arreglo_factura["id_empresa_sat"] = $this->repository->getIdEmpresa($this->arreglo_factura["receptor"]);
        $proveedor = $this->repository->getProveedorSAT($this->arreglo_factura["emisor"], $this->arreglo_factura["id_empresa_sat"]);
        $this->arreglo_factura["id_proveedor_sat"] = $proveedor["id_proveedor"];
        if ($proveedor["nuevo"] > 0) {
            $this->log["proveedores_nuevos"] += 1;
        }*/
        //return 1;
    }

    private function getFecha(string $fecha)
    {
        $fecha_xml = DateTime::createFromFormat('Y-m-d\TH:i:s', $fecha);
        if (!$fecha_xml) {
            $fecha_xml = DateTime::createFromFormat('Y-m-d\TH:i:s.u', $fecha);
            if (!$fecha_xml) {
                $fecha_xml = substr($fecha, 0, 19);
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

    private function getValidacionCFDI33($arreglo_cfd)
    {
        return SatQueryRequest::soapRequest($arreglo_cfd['emisor']['rfc'], $arreglo_cfd['receptor']['rfc'],$arreglo_cfd['total'], $arreglo_cfd['complemento']['uuid'], substr($arreglo_cfd['sello'],-8));
    }

    public function validaCFDI33($xml = null, $datos)
    {
        $usa_servicio = config('app.env_variables.SERVICIO_CFDI_EN_USO');
        if ($usa_servicio == 1) {
            if ($xml == null) {
                $xml = $this->archivo_xml;
            }
            $respuesta = $this->getValidacionCFDI33($datos);

            if ($respuesta->Estado == 'No Encontrado') {
                $omitido = $this->getEsOmitido($respuesta->Estado, $this->arreglo_factura["emisor"]["rfc"], $this->arreglo_factura["uuid"]);
                if ($omitido == 0) {
                    event(new IncidenciaCI(
                        ["id_tipo_incidencia" => 3,
                            "rfc" => $this->arreglo_factura["emisor"]["rfc"],
                            "empresa" => $this->arreglo_factura["emisor"]["nombre"],
                            "mensaje" => $respuesta->CodigoEstatus . ' ' . $respuesta->Estado,
                            "xml" => $xml
                        ]
                    ));
                    abort(500, "Aviso SAT:\nError al encontrar el comprobante: " . $respuesta->Estado);
                }
            }

            if ($respuesta->CodigoEstatus == "N - 601: La expresión impresa proporcionada no es válida.") {
                $omitido = $this->repository->getEsOmitido($respuesta->CodigoEstatus, $datos["emisor"]["rfc"], $datos["complemento"]["uuid"]);
                if ($omitido == 0) {
                    event(new IncidenciaCI(
                        ["id_tipo_incidencia" => 13,
                            "rfc" => $this->arreglo_factura["emisor"]["rfc"],
                            "empresa" => $this->arreglo_factura["emisor"]["nombre"],
                            "mensaje" => $respuesta->CodigoEstatus . ' ' . $respuesta->Estado,
                            "xml" => $xml
                        ]
                    ));
                    abort(500, "Aviso SAT:\nError en la validación de la estructura del comprobante: " . $respuesta->CodigoEstatus . ' Estado: ' . $respuesta->Estado);
                }
            }

            if ($respuesta->Estado == 'Cancelado') {
                $omitido = $this->getEsOmitido($respuesta->Estado, $this->arreglo_factura["emisor"]["rfc"], $this->arreglo_factura["uuid"]);
                if ($omitido == 0) {
                    event(new IncidenciaCI(
                        ["id_tipo_incidencia" => 18,
                            "rfc" => $this->arreglo_factura["emisor"]["rfc"],
                            "empresa" => $this->arreglo_factura["emisor"]["nombre"],
                            "mensaje" => $respuesta->CodigoEstatus . ' ' . $respuesta->Estado . ' ' . $respuesta->EstatusCancelacion,
                            "xml" => $xml
                        ]
                    ));
                    abort(500, "Aviso SAT:\nError el comprobante se encuentra: " . $respuesta->Estado);
                }
            }

            if ($respuesta->EstatusCancelacion != [] && $respuesta->EstatusCancelacion == 'En proceso') {
                $omitido = $this->repository->getEsOmitido($respuesta->EstatusCancelacion, $this->arreglo_factura["emisor"]["rfc"], $this->arreglo_factura["complemento"]["uuid"]);
                if ($omitido == 0) {
                    event(new IncidenciaCI(
                        ["id_tipo_incidencia" => 18,
                            "rfc" => $this->arreglo_factura["emisor"]["rfc"],
                            "empresa" => $this->arreglo_factura["emisor"]["nombre"],
                            "mensaje" => $respuesta->CodigoEstatus . ' ' . $respuesta->Estado . ' ' . $respuesta->EstatusCancelacion,
                            "xml" => $xml
                        ]
                    ));
                    abort(500, "Aviso SAT:\nError en la validación del comprobante: " . $respuesta->EstatusCancelacion);
                }
            }
        }
    }

    public function getEsOmitido($mensaje, $rfc_emisor, $uuid)
    {
        $explode = explode("-",$mensaje);
        $codigo = trim($explode[0]);
        $existe = AvisoSATOmitir::where("rfc_emisor",$rfc_emisor)
            ->where("clave",$codigo)
            ->where("estado",1)
            ->count();
        if($existe == 1){
            return $existe;
        } else {
            $existe = AvisoSATOmitir::where("uuid",$uuid)
                ->where("clave",$codigo)
                ->where("estado",1)
                ->count();
            return $existe;
        }
    }

    public function validaVigente()
    {
        $respuesta = $this->getValidacionCFDI33($this->arreglo_factura);
        $env_servicio = config('app.env_variables.SERVICIO_CFDI_ENV');

        if ($env_servicio === "production") {
            if ($respuesta->Estado == 'Cancelado') {
                return false;
            }
            return true;
        }
    }


    public function guardarXmlEnADD(){
        $xml_fuente = $this->archivo_xml;
        $xml_array = $this->arreglo_factura;
        $this->logs = [];

        if(in_array($this->arreglo_factura['tipo_comprobante'], ["I", "E"])) {

            $xml_split = explode('base64,', $xml_fuente);
            $xml = base64_decode($xml_split[1]);

            $obra = Obra::find(Context::getIdObra());
            if ($obra->datosContables->BDContPaq != "") {
                $this->logs[] = "Inicia";
                DB::purge('cntpq');
                Config::set('database.connections.cntpq.database', $obra->datosContables->BDContPaq);
                try {
                    $parametros = Parametro::first();
                } catch (Exception $e) {
                    $this->logs[] = "Error de lectura a la base de datos: " . Config::get('database.connections.cntpq.database') . ".";
                }

                try {
                    $arreglo_bbdd = $this->existDb($parametros->GuidDSL);
                    if ($arreglo_bbdd == false) {
                        $this->logs[] = "Error existDb";
                    }
                } catch (Exception $e) {
                    $this->logs[] = "Error existDb catch: " . $e->getMessage();
                }

                try {
                    $val_insercionCertificado = $this->insUpdCertificate($xml_array['certificado'], $xml_array['no_certificado'], $xml_array['emisor']['rfc'], $xml_array['emisor']['nombre']);
                    if (!$val_insercionCertificado) {
                        $this->logs[] = "Error insUpdCertificate";
                    }
                } catch (Exception $e) {
                    $this->logs[] = "Error insUpdCertificate catch: " . $e->getMessage();
                }
                $duplicado = false;
                try {
                    if ($duplicado = $this->buscarCfdiDuplicado($arreglo_bbdd[0]['NameDB'], $xml_array['complemento']['uuid'])) {
                        $this->logs[] = "CFDI ya existente en ADD";
                    }
                } catch (Exception $e) {
                    $this->logs[] = "Error buscarCfdiDuplicado catch: " . $e->getMessage();
                }

                if (!$duplicado) {
                    $guid_doc_metadata = Uuid::generate()->string;

                    try {
                        $va_insert_xml = $this->spInsUpdDocument($xml, $arreglo_bbdd[0]['NameDB'], $arreglo_bbdd[1]['NameDB'], $arreglo_bbdd[3]['NameDB'], $arreglo_bbdd[2]['NameDB'], $guid_doc_metadata, $xml_array['fecha_hora'], $xml_array['emisor']['rfc'], $xml_array['folio']);
                        if (!$va_insert_xml) {
                            $this->logs[] = "Error spInsUpdDocument";
                        } else {
                            $this->logs[] = ["tipo" => 1, "descripcion" => "Envío éxitoso, comprobante con GUID: " . $guid_doc_metadata . " en base de datos: " . Config::get('database.connections.cntpqdm.database')];
                        }
                    } catch (Exception $e) {
                        $this->logs[] = "Error spInsUpdDocument catch: " . $e->getMessage();
                    }
                }

                $this->logs[] = "Finaliza";
            }
        }
        return $this->logs;
    }

    private function existDb($guidCompany){
        try{
            $resp = DB::connection('cntpq')->select(DB::raw("exec [DB_Directory].[dbo].[spExistDB] @GuidCompany = '$guidCompany'"));
            $resp_ = json_decode(json_encode($resp), true);
            return $resp_;
        }catch(Exception $e){
            throw new Exception($e->getMessage(),500);
        }
        return false;
    }

    private function insUpdCertificate($llave, $no_serie, $rfc, $r_social){
        $guidDoc = Uuid::generate()->string;
        $issuer_name = 'OID.1.2.840.113549.1.9.2=Responsable: Administración Central de Servicios Tributarios al Contribuyente, OID.2.5.4.45=SAT970701NN3, L=Cuauhtémoc, S=Distrito Federal, C=MX, PostalCode=06300, STREET="Av. Hidalgo 77, Col. Guerrero", E=acods@sat.gob.mx, OU=Administración de Seguridad de la Información, O=Servicio de Administración Tributaria, CN=A.C. del Servicio de Administración Tributaria';
        $subject_name = 'OU=UNICA, SERIALNUMBER=" / ", OID.2.5.4.45='.$rfc.' / , O='.$r_social.', OID.2.5.4.41='.$r_social.', CN='.$r_social;
        try{
            $resp = DB::connection('cntpq')
                ->update("SET ANSI_NULLS ON; SET ANSI_WARNINGS ON; exec [DB_Directory].[dbo].[spInsUpdCertificate] @GuidDocument = 'DD41F3B0-D47A-11EB-82DA-E1114F8D5A0B', @LlavePublica='$llave', @NumeroSerie='$no_serie', @FechaInicial='',
                @FechaFinal='',@SubjectName='$subject_name', @IssuerName='$issuer_name', @IsTesting=0");

            $val = DB::connection('cntpq')->select(DB::raw("SELECT top 1 * FROM [DB_Directory].[dbo].[Certificates] WHERE NumeroSerie='$no_serie'"));
            return $val != false;
        }catch(Exception $e){
            throw new Exception("Error de ejecución del sp spInsUpdCertificate en la base de datos DB_Directory".$e->getMessage().$e->getLine(),500);
        }
        return false;
    }

    private function buscarCfdiDuplicado($base_datos, $uuid){
        try{
            DB::purge('cntpqdm');
            Config::set('database.connections.cntpqdm.database', $base_datos);
            $resp = DB::connection('cntpqdm')->select(DB::raw("SELECT Documento.GuidDocument GuidDocument FROM  Documento WITH(NOLOCK)
        LEFT JOIN Comprobante WITH(NOLOCK) ON Comprobante.GuidDocument = Documento.GuidDocument
        WHERE Comprobante.UUID='$uuid' "));
            return count($resp) > 0;
        }catch(Exception $e){
            throw new Exception("Error de lectura a la base de datos: ".Config::get('database.connections.cntpqdm.database').$e->getMessage().$e->getLine(),500);
        }
    }

    private function spInsUpdDocument($xml, $db_doc_metadata, $db_doc_content, $db_other_content, $db_other_metadata, $guid, $doc_date, $rfc, $folio){
        DB::purge('cntpqdm');
        Config::set('database.connections.cntpqdm.database', $db_doc_metadata);
        $hash = md5($guid).'=';

        $micro_date = microtime();
        $date_array = explode(" ",$micro_date);
        $date = date("Y-m-d",$date_array[1]).'T'. date('H:i:s', $date_array[1]) . str_replace('0.', '.', $date_array[0]).'-05:00';

        $xml_data = $this->del_string_between($xml, '<cfdi:Conceptos>', '</cfdi:Conceptos>');
        $xml_data = $this->del_string_between($xml, '<?', '?>');

        $pXmlFile = '<Metadata xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="1.0"
        Hash="'.$hash.'" Status="active" TimeStamp="'.$date.'" FilePermissions="R" GuidDocument="'.$guid.'" Type="CFDI" xmlns="http://www.contpaqi.com">
        <Document><Document xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Type="XML" xmlns="">' . $xml_data.
            '</Document></Document><MetadataApp><SourceFile Value="'.$guid.'.xml" xmlns="" /></MetadataApp></Metadata>';

        $pXmlFile = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($pXmlFile));
        $pXmlFile = preg_replace('/[ ]{2,}|[\r]/', ' ', trim($pXmlFile));
        $pXmlFile = preg_replace('/[ ]{2,}|[\n]/', ' ', trim($pXmlFile));

        try{
            $resp = DB::connection('cntpqdm')
                ->update(DB::raw("DECLARE @return_value int  EXEC [$db_doc_metadata].[dbo].[spInsUpdDocument]  @pXmlFile = N'$pXmlFile', @pDeleteDocument=0, @pSobreEscribe=0, @filename=NULL SELECT 'Return Value' = @return_value"));

        }catch(Exception $e){
            throw new Exception("-Error de ejecución de sp spInsUpdDocument en la base de datos: ".Config::get('database.connections.cntpqdm.database')." respuesta: ".$e->getMessage().$e->getLine(),500);
        }

        $val = DB::connection('cntpqdm')->select(DB::raw("SELECT top 1 * FROM [$db_doc_metadata].[dbo].[Comprobante] WHERE [GuidDocument]='$guid'"));
        if(count($val) == 0){
            throw new Exception("respuesta spInsUpdDocument: ".$resp." No se encontro el comprobante con el GUID: ".$guid." en la base de datos: ".Config::get('database.connections.cntpqdm.database'));
        }

        $conceptos = $this->get_string_between($xml, '<cfdi:Conceptos>', '</cfdi:Conceptos>');
        $conceptos = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($conceptos));
        $array_concepto = [];
        do{
            $array_concepto[] = $this->get_string_between($conceptos, '<cfdi:Concepto', '</cfdi:Concepto>');
            $conceptos = $this->del_string_between($conceptos, '<cfdi:Concepto', '</cfdi:Concepto>');
        } while(strlen($conceptos) > 50);

        try{
            foreach($array_concepto as $key => $concepto){
                $filename = $guid . '.xml';
                $conceptNumber = $key + 1;
                $pXml_Node = '<cfdi:Concepto xmlns:cfdi="http://www.sat.gob.mx/cfd/3" ' . $concepto . '</cfdi:Concepto>';
                $resp = DB::connection('cntpqdm')
                    ->update("exec [$db_doc_metadata].[dbo].[spInsConcept]  @pGuidDocument=N'$guid',@pXml_Node=N'$pXml_Node', @fileName=N'$filename', @conceptNumber=$conceptNumber");
            }

        }catch(Exception $e){
            throw new Exception("Error de ejecución de sp spInsConcept en la base de datos: ".Config::get('database.connections.cntpqdm.database').$e->getMessage().$e->getLine(),500);
        }

        $creation_date = date("Y-m-d H:i:s",$date_array[1]);
        try{
            DB::purge('cntpqdc');
            Config::set('database.connections.cntpqdc.database', $db_doc_content);
            $resp = DB::connection('cntpqdc')
                ->update("exec [$db_doc_content].[dbo].[spSaveDocument]  @GuidDocument=N'$guid',@DocumentType=N'CFDI', @fileName=N'$filename' ,@Content=N'$xml'
                            ,@SubDirectory=N'',@DocumentDate=N'$doc_date',@CreationDate=N'$creation_date'");
        }catch(Exception $e){
            throw new Exception("Error de ejecución de sp spSaveDocument en la base de datos: ".Config::get('database.connections.cntpqdc.database').$e->getMessage().$e->getLine(),500);
        }

        try{
            DB::purge('cntpqdc');
            Config::set('database.connections.cntpqdc.database', $db_doc_content);
            $resp = DB::connection('cntpqdc')
                ->select("SELECT top 1 * FROM [$db_doc_content].[dbo].[DocumentContent] where [GuidDocument] = '$guid' ");
            $content = ltrim($resp[0]->Content);
            if(substr($content, 0, 1) == '?'){
                $content = str_replace('?<', '<', $content);
                $upd = DB::connection('cntpqdc')
                    ->update("UPDATE [$db_doc_content].[dbo].[DocumentContent] set Content = '$content' where [GuidDocument] = '$guid' ");
            }
        }catch(Exception $e){
            throw new Exception("Error de ejecución de validación de signo ? al inicio del XML: ".Config::get('database.connections.cntpqdc.database').$e->getMessage().$e->getLine(),500);
        }


        $guid_vr = Uuid::generate()->string;
        $filename_vr = $guid_vr . '.xml';
        $val_result = $this->validationResult($guid_vr, $date, $doc_date, $rfc, $folio);
        try{
            DB::purge('cntpqom');
            Config::set('database.connections.cntpqom.database', $db_other_metadata);
            $resp = DB::connection('cntpqom')
                ->update("exec [$db_other_metadata].[dbo].[spInsUpdDocument]  @pXmlFile = '$val_result', @pDeleteDocument=0, @pSobreEscribe=0");
        }catch(Exception $e){
            throw new Exception("Error de ejecución de sp spInsUpdDocument en la base de datos: ".Config::get('database.connections.cntpqom.database').$e->getMessage(),500);
        }

        try{
            $fecha_sf = date('Y-m-d');
            $resp = DB::connection('cntpqom')
                ->update("exec [$db_other_metadata].[dbo].[spCreateReferences]  @GuidRel =N'$guid' , @RelatedGuidDocuments=N'$guid_vr'
                        ,@ApplicationType=N'ADD',@TipoDoc=N'ValidationResult',@Fecha=N'$fecha_sf',@Comment=N'Acuse Validación Comprobante $doc_date $rfc $folio '");
        }catch(Exception $e){
            throw new Exception("Error de ejecución de sp spCreateReferences en la base de datos: ".Config::get('database.connections.cntpqom.database').$e->getMessage(),500);
        }

        $val_result_corto = $this->get_string_between($val_result, '<ValidationResult', '</ValidationResult>');
        $val_result_corto = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><ValidationResult'. $val_result_corto .'</ValidationResult>';
        try {
            DB::purge('cntpqoc');
            Config::set('database.connections.cntpqoc.database', $db_other_content);
            $resp = DB::connection('cntpqoc')
                ->update("exec [$db_other_content].[dbo].[spSaveDocument]  @GuidDocument=N'$guid_vr',@DocumentType=N'VALIDATIONRESULT', @fileName=N'$filename_vr' ,@Content=N'$val_result_corto'
                            ,@SubDirectory=N'',@DocumentDate=N'$creation_date',@CreationDate=N'$creation_date'");
        }
        catch(Exception $e){
            throw new Exception("Error de ejecución de sp spSaveDocument  en la base de datos: ".Config::get('database.connections.cntpqoc.database').$e->getMessage(),500);
        }
        try {
            $resp = DB::connection('cntpqdm')
                ->update("exec [$db_doc_metadata].[dbo].[spUpdDocumento]  @GuidDocument =N'$guid',@ProcessApp=N'',@UserResponsibleApp=N'',
                            @ReferenceApp=N'',@NotesApp=N'',@MetadataEstatusApp=N'Timbrado',@ValidationStatus=N'OK' ");
        }
        catch(Exception $e){
            throw new Exception("Error de ejecución de sp spUpdDocumento  en la base de datos: ".Config::get('database.connections.cntpqdm.database').$e->getMessage(),500);
        }

        return true;

    }

    private function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    private function del_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $len = strpos($string, $end, $ini)-$ini;
        $texto =  substr($string, $ini, $len);
        return str_replace($texto.$end, '', $string);
    }

    private function validationResult($guid, $date, $date_xml, $rfc, $folio){
        $hash = md5($guid).'=';
        return '<?xml version="1.0" encoding="utf-8"?><Metadata xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="1.0"
        Hash="'.$hash.'" Status="active" TimeStamp="'.$date.'" FilePermissions="R" GuidDocument="'.$guid.'" Type="ValidationResult"
        xmlns="http://www.contpaqi.com"><Document><Document xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Type="XML" xmlns="">
        <ValidationResult documentType="CFDI" IsDuplicated="false" IsValid="true"><RFCIssuer>'.$rfc.'</RFCIssuer><DateIssue>'.$date_xml.'</DateIssue><Serial></Serial><Number>'.$folio.'</Number>
        <ValidationItemResult validationResult="OK" descriptionValidation="Codificación del CFD/CFDI es UTF-8 . " codeValidation="1.1" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El XML es un comprobante " codeValidation="1.2" /><ValidationItemResult validationResult="OK" descriptionValidation="Estructura  "  codeValidation="1.3" />
        <ValidationItemResult validationResult="OK" descriptionValidation="La versión del comprobante es correcta a su fecha de generación" codeValidation="1.4" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El número de certificado del comprobante corresponde al certificado reportado " codeValidation="2.1" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El certificado del comprobante en base 64 es correcto" codeValidation="2.2" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El certificado del comprobante fue emitido por el SAT " codeValidation="2.3" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El certificado del comprobante corresponde a un CSD o FIEL " codeValidation="2.4" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El sello del comprobante es válido para el certificado reportado " codeValidation="2.8" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El certificado del comprobante no debe corresponder a un certificado de prueba " codeValidation="2.9" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El certificado corresponde al RFC del Emisor" codeValidation="3.1" /><ValidationItemResult validationResult="OK"
        descriptionValidation="CFDI Se encontró el complemento Timbre Fiscal Digital " codeValidation="4.3" /><ValidationItemResult validationResult="OK"
        descriptionValidation="CFDI Se encontró el certificado  del PAC   (00001000000504587508)" codeValidation="4.4" /><ValidationItemResult validationResult="OK"
        descriptionValidation="CFDI El sello del Timbre Fiscal Digital es válido " codeValidation="4.7" /><ValidationItemResult validationResult="OK"
        descriptionValidation="CFDI El certificado con el que se generó el Timbre Fiscal Digital no debe ser un certificado de prueba " codeValidation="4.8" /><ValidationItemResult
        validationResult="OK" descriptionValidation="CFDI El certificado con el que se generó el Timbre Fiscal Digital fue emitido para un PAC " codeValidation="4.9" /><ValidationItemResult
        validationResult="OK" descriptionValidation="CFDI El sello CFD del timbre corresponde con el sello del comprobante " codeValidation="4.1" /><ValidationItemResult validationResult="OK"
        descriptionValidation="En cargar Recibidos: El RFC del comprobante Recibido corresponde con el RFC de la empresa " codeValidation="5.1" /></ValidationResult></Document>
        </Document><MetadataApp><SourceFile Value="'.$guid.'.xml" xmlns="" /></MetadataApp></Metadata>';
    }
}
