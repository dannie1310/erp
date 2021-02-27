<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/07/2020
 * Time: 05:32 PM
 */

namespace App\Utils;
use DateTime;

class CFD
{
    protected $arreglo_factura;
    protected $log;
    protected $archivo_xml;

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
    }

    public function getArregloFactura()
    {
        $this->arreglo_factura = [];
        try {
            libxml_use_internal_errors(true);
            $factura_xml = simplexml_load_file($this->archivo_xml);
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
        } else if ($factura_xml["Version"] == "3.3") {
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
            $this->arreglo_factura["version"] = (string)$factura_xml["Version"];
            $this->arreglo_factura["moneda"] = (string)$factura_xml["Moneda"];
            $this->arreglo_factura["tipo_cambio"] = (string)$factura_xml["TipoCambio"];
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
        } catch (\Exception $e) {
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }

        try {
            $ns = $factura_xml->getNamespaces(true);
            $impuestos = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Impuestos');
            if (count($impuestos) >= 1) {
                $this->arreglo_factura["total_impuestos_trasladados"] = (float)$impuestos[count($impuestos) - 1]["TotalImpuestosTrasladados"];
            } else {
                $this->arreglo_factura["total_impuestos_trasladados"] = (float)0;
            }
            $traslados = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Traslado');

            $i = 0;
            foreach ($traslados as $traslado) {
                if (!(float)$traslado["Base"] > 0) {
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
            }

            if (count($impuestos) >= 1) {
                $this->arreglo_factura["total_impuestos_retenidos"] = (float)$impuestos[count($impuestos) - 1]["TotalImpuestosRetenidos"];
            } else {
                $this->arreglo_factura["total_impuestos_retenidos"] = (float)0;
            }

            $retenciones = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Retencion');

            $iret = 0;
            foreach ($retenciones as $retencion) {
                if (!(float)$retencion["Base"] > 0) {
                    if ($retencion["Impuesto"] == "002") {
                        $this->arreglo_factura["importe_iva_retenido"] = (float)$retencion["Importe"];
                        $this->arreglo_factura["tasa_iva_retenido"] = (float)$retencion["TasaOCuota"];
                    }
                    $this->arreglo_factura["retenciones"][$iret]["impuesto"] = (string)$retencion["Impuesto"];
                    $this->arreglo_factura["retenciones"][$iret]["importe"] = (float)$retencion["Importe"];
                    $iret++;
                }
            }

            $conceptos = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Concepto');
            $i = 0;
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
                $traslados_concepto = $factura_xml->xpath("/cfdi:Comprobante/cfdi:Conceptos/cfdi:Concepto/cfdi:Impuestos/cfdi:Traslados/cfdi:Traslado");
                $itc = 0;
                foreach ($traslados_concepto as $traslado_concepto) {
                    $this->arreglo_factura["conceptos"][$i]["traslados"][$itc]["base"] = (float)$traslado_concepto["Base"];
                    $this->arreglo_factura["conceptos"][$i]["traslados"][$itc]["impuesto"] = (string)$traslado_concepto["Impuesto"];
                    $this->arreglo_factura["conceptos"][$i]["traslados"][$itc]["importe"] = (float)$traslado_concepto["Importe"];
                    $this->arreglo_factura["conceptos"][$i]["traslados"][$itc]["tasa_o_cuota"] = (float)$traslado_concepto["TasaOCuota"];
                    $this->arreglo_factura["conceptos"][$i]["traslados"][$itc]["tipo_factor"] = (string)$traslado_concepto["TipoFactor"];
                    $itc++;
                }

                $retenciones_concepto = $factura_xml->xpath("/cfdi:Comprobante/cfdi:Conceptos/cfdi:Concepto/cfdi:Impuestos/cfdi:Retenciones/cfdi:Retencion");
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
        //$this->arreglo_factura["subtotal"] = $this->arreglo_factura["total"] - $this->arreglo_factura["total_impuestos_trasladados"];
        /*$this->arreglo_factura["id_empresa_sat"] = $this->repository->getIdEmpresa($this->arreglo_factura["receptor"]);
        $proveedor = $this->repository->getProveedorSAT($this->arreglo_factura["emisor"], $this->arreglo_factura["id_empresa_sat"]);
        $this->arreglo_factura["id_proveedor_sat"] = $proveedor["id_proveedor"];

        if ($proveedor["nuevo"] > 0) {
            $this->log["proveedores_nuevos"] += 1;
        }*/
        //return 1;
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
        $this->arreglo_factura["moneda"] = (string)$factura_xml["Moneda"];
        $this->arreglo_factura["tipo_cambio"] = (string)$factura_xml["TipoCambio"];

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
}
