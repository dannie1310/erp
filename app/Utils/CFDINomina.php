<?php

namespace App\Utils;
use App\Events\IncidenciaCI;
use App\Http\Requests\SatQueryRequest;
use DateTime;


class CFDINomina
{
    protected $arreglo_cfdi;
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
    }

    public function getArreglo()
    {
        $this->arreglo_cfdi = [];
        $this->arreglo_cfdi["xml"] = $this->archivo_xml;
        try {
            libxml_use_internal_errors(true);
            $cfdi_xml = simplexml_load_file($this->archivo_xml);
            if($cfdi_xml === false)
            {
                $cfdi_xml = simplexml_load_string($this->archivo_xml);
            }

            if(!$cfdi_xml){
                $errors = libxml_get_errors();
            }
        } catch (\Exception $e) {
            //abort(500, "Hubo un error al leer el archivo XML proporcionado. " . ' Ln.' . $e->getLine() . ' ' . $e->getMessage());
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }

        $this->arreglo_cfdi["version"] = (string)$cfdi_xml["Version"];
        $this->setArreglo33($cfdi_xml);

        return $this->arreglo_cfdi;
    }

    private function setArreglo33($cfdi_xml)
    {
        try {
            $this->arreglo_cfdi["fecha"] = $this->getFecha((string)$cfdi_xml["Fecha"]);
            $this->arreglo_cfdi["fecha_hora"] = $this->getFechaHora((string)$cfdi_xml["Fecha"]);

            $this->arreglo_cfdi["forma_pago"] = (string)$cfdi_xml["FormaPago"];
            $this->arreglo_cfdi["moneda"] = (string)$cfdi_xml["Moneda"];
            $this->arreglo_cfdi["metodo_pago"] = (string)$cfdi_xml["MetodoPago"];
            $this->arreglo_cfdi["serie"] = (string)$cfdi_xml["Serie"];
            $this->arreglo_cfdi["folio"] = (string)$cfdi_xml["Folio"];
            $this->arreglo_cfdi["lugar_expedicion"] = (string)$cfdi_xml["LugarExpedicion"];
            $this->arreglo_cfdi["subtotal"] = (float)$cfdi_xml["SubTotal"];
            $this->arreglo_cfdi["descuento"] = (float)$cfdi_xml["Descuento"];
            $this->arreglo_cfdi["total"] = (float)$cfdi_xml["Total"];
            $this->arreglo_cfdi["tipo_comprobante"] = (string)$cfdi_xml["TipoDeComprobante"];


            $emisor = $cfdi_xml->xpath('//cfdi:Comprobante//cfdi:Emisor')[0];
            $this->arreglo_cfdi["emisor"]["rfc"] = (string)$emisor["Rfc"][0];
            $this->arreglo_cfdi["emisor"]["nombre"] = (string)$emisor["Nombre"][0];
            $this->arreglo_cfdi["emisor"]["regimen_fiscal"] = (string)$emisor["RegimenFiscal"][0];

            $this->arreglo_cfdi["rfc_emisor"] = $this->arreglo_cfdi["emisor"]["rfc"];
            $this->arreglo_cfdi["regimen_fiscal_emisor"] = $this->arreglo_cfdi["emisor"]["regimen_fiscal"];

            $receptor = $cfdi_xml->xpath('//cfdi:Comprobante//cfdi:Receptor')[0];
            $this->arreglo_cfdi["receptor"]["rfc"] = (string)$receptor["Rfc"][0];
            $this->arreglo_cfdi["receptor"]["nombre"] = (string)$receptor["Nombre"][0];
            $this->arreglo_cfdi["receptor"]["domicilio_fiscal_receptor"] = (string)$receptor["DomicilioFiscalReceptor"][0];
            $this->arreglo_cfdi["receptor"]["regimen_fiscal_receptor"] = (string)$receptor["RegimenFiscalReceptor"][0];
            $this->arreglo_cfdi["receptor"]["uso_cfdi"] = (string)$receptor["UsoCFDI"][0];

            $this->arreglo_cfdi["rfc_receptor"] = $this->arreglo_cfdi["receptor"]["rfc"];
            $this->arreglo_cfdi["domicilio_fiscal_receptor"] = $this->arreglo_cfdi["receptor"]["domicilio_fiscal_receptor"];
            $this->arreglo_cfdi["regimen_fiscal_receptor"] = $this->arreglo_cfdi["receptor"]["regimen_fiscal_receptor"];
            $this->arreglo_cfdi["uso_cfdi_receptor"] = $this->arreglo_cfdi["receptor"]["uso_cfdi"];

            $this->arreglo_cfdi["conceptos"] = $this->getConceptos($cfdi_xml);
            $this->arreglo_cfdi["percepciones"] = $this->getPercepciones($cfdi_xml);
            $this->arreglo_cfdi["deducciones"] = $this->getDeducciones($cfdi_xml);
            $this->arreglo_cfdi["otros_pagos"] = $this->getOtrosPagos($cfdi_xml);
            $this->arreglo_cfdi["receptor_nomina"] = $this->getReceptorNomina($cfdi_xml);
            $this->arreglo_cfdi["emisor_nomina"] = $this->getEmisorNomina($cfdi_xml);

            if(key_exists("registro_patronal", $this->arreglo_cfdi["emisor_nomina"]))
            {
                $this->arreglo_cfdi["emisor"]["registro_patronal"] = $this->arreglo_cfdi["emisor_nomina"]["registro_patronal"];
            }


            if(key_exists("nss", $this->arreglo_cfdi["receptor_nomina"]))
            {
                $this->arreglo_cfdi["receptor"]["nss"] = $this->arreglo_cfdi["receptor_nomina"]["nss"];
            }
            if(key_exists("curp", $this->arreglo_cfdi["receptor_nomina"]))
            {
                $this->arreglo_cfdi["receptor"]["curp"] = $this->arreglo_cfdi["receptor_nomina"]["curp"];
            }
            if(key_exists("departamento", $this->arreglo_cfdi["receptor_nomina"]))
            {
                $this->arreglo_cfdi["departamento"] = $this->arreglo_cfdi["receptor_nomina"]["departamento"];
            }
            if(key_exists("puesto", $this->arreglo_cfdi["receptor_nomina"]))
            {
                $this->arreglo_cfdi["puesto"] = $this->arreglo_cfdi["receptor_nomina"]["puesto"];
            }

        } catch (\Exception $e) {
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }

        $this->arreglo_cfdi["tipo_relacion"] = '';
        $this->arreglo_cfdi["cfdi_relacionado"]  ='';

        $CFDIRelacionado = $cfdi_xml->xpath('//cfdi:Comprobante//cfdi:CfdiRelacionados');
        if(count($CFDIRelacionado)>0){
            $CFDIRelacionado = $cfdi_xml->xpath('//cfdi:Comprobante//cfdi:CfdiRelacionados')[0];
            $this->arreglo_cfdi["tipo_relacion"] = (string)$CFDIRelacionado["TipoRelacion"][0];
            $this->arreglo_cfdi["cfdi_relacionado"] = (string)$cfdi_xml->xpath('//cfdi:Comprobante//cfdi:CfdiRelacionados//cfdi:CfdiRelacionado')[0]["UUID"];
        }

        try {

            $ns = $cfdi_xml->getNamespaces(true);
            if (key_exists("cfdi", $ns)) {
                $cfdi_xml->registerXPathNamespace('c', $ns['cfdi']);
            }
            $cfdi_xml->registerXPathNamespace('t', $ns['tfd']);
            $complemento = $cfdi_xml->xpath('//t:TimbreFiscalDigital')[0];
            $this->arreglo_cfdi["uuid"] = (string)$complemento["UUID"][0];
            $this->arreglo_cfdi["complemento"]["uuid"] = (string)$complemento["UUID"][0];

        } catch (\Exception $e) {
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }
    }

    private function getConceptos($cfdi_xml)
    {
        $conceptos = [];

        $conceptos_cfdi = $cfdi_xml->xpath('//cfdi:Comprobante//cfdi:Concepto');
        $i = 0;
        foreach ($conceptos_cfdi as $concepto_cfdi) {
            $conceptos[$i]["clave_prod_serv"] = (string)$concepto_cfdi["ClaveProdServ"];
            $conceptos[$i]["cantidad"] = (float)$concepto_cfdi["Cantidad"];
            $conceptos[$i]["clave_unidad"] = (string)$concepto_cfdi["ClaveUnidad"];
            $conceptos[$i]["descripcion"] = (string)$concepto_cfdi["Descripcion"];
            $conceptos[$i]["obj_imp"] = (string)$concepto_cfdi["ObjetoImp"];
            $conceptos[$i]["valor_unitario"] = (float)$concepto_cfdi["ValorUnitario"];
            $conceptos[$i]["importe"] = (float)$concepto_cfdi["Importe"];
            $conceptos[$i]["descuento"] = (float)$concepto_cfdi["Descuento"];
            $i++;
        }
        return $conceptos;
    }

    private function getReceptorNomina($cfdi_xml)
    {
        $ns = $cfdi_xml->getNamespaces(true);
        if (key_exists("nomina12", $ns)) {
            $cfdi_xml->registerXPathNamespace('n12', $ns['nomina12']);
        }

        $receptor_nomina = [];
        $receptor_nomina_cfdi = $cfdi_xml->xpath('//n12:Receptor')[0];

        $receptor_nomina["nss"] = (string)$receptor_nomina_cfdi["NumSeguridadSocial"];
        $receptor_nomina["curp"] = (string)$receptor_nomina_cfdi["Curp"];
        $receptor_nomina["departamento"] = (string)$receptor_nomina_cfdi["Departamento"];
        $receptor_nomina["puesto"] = (string)$receptor_nomina_cfdi["Puesto"];

        return $receptor_nomina;
    }

    private function getEmisorNomina($cfdi_xml)
    {
        $ns = $cfdi_xml->getNamespaces(true);
        if (key_exists("nomina12", $ns)) {
            $cfdi_xml->registerXPathNamespace('n12', $ns['nomina12']);
        }

        $emisor_nomina = [];
        $emisor_nomina_cfdi = $cfdi_xml->xpath('//n12:Emisor')[0];

        $emisor_nomina["registro_patronal"] = (string)$emisor_nomina_cfdi["RegistroPatronal"];

        return $emisor_nomina;
    }

    private function getPercepciones($cfdi_xml)
    {
        $ns = $cfdi_xml->getNamespaces(true);
        if (key_exists("nomina12", $ns)) {
            $cfdi_xml->registerXPathNamespace('n12', $ns['nomina12']);
        }
        $percepciones = [];

        $percepciones_cfdi = $cfdi_xml->xpath('//n12:Percepcion');
        $i = 0;
        foreach ($percepciones_cfdi as $percepcion_cfdi) {
            $percepciones[$i]["tipo_percepcion"] = (int)$percepcion_cfdi["TipoPercepcion"];
            $percepciones[$i]["clave"] = (int)$percepcion_cfdi["Clave"];
            $percepciones[$i]["concepto"] = (string)$percepcion_cfdi["Concepto"];
            $percepciones[$i]["importe_gravado"] = (float)$percepcion_cfdi["ImporteGravado"];
            $percepciones[$i]["importe_exento"] = (float)$percepcion_cfdi["ImporteExento"];

            $i++;
        }
        return $percepciones;
    }

    private function getDeducciones($cfdi_xml)
    {
        $ns = $cfdi_xml->getNamespaces(true);
        if (key_exists("nomina12", $ns)) {
            $cfdi_xml->registerXPathNamespace('n12', $ns['nomina12']);
        }
        $deducciones = [];

        $deducciones_cfdi = $cfdi_xml->xpath('//n12:Deduccion');
        $i = 0;
        foreach ($deducciones_cfdi as $deduccion_cfdi) {
            $deducciones[$i]["tipo_deduccion"] = (int)$deduccion_cfdi["TipoDeduccion"];
            $deducciones[$i]["clave"] = (int)$deduccion_cfdi["Clave"];
            $deducciones[$i]["concepto"] = (string)$deduccion_cfdi["Concepto"];
            $deducciones[$i]["importe"] = (float)$deduccion_cfdi["Importe"];

            $i++;
        }
        return $deducciones;
    }

    private function getOtrosPagos($cfdi_xml)
    {
        $ns = $cfdi_xml->getNamespaces(true);
        if (key_exists("nomina12", $ns)) {
            $cfdi_xml->registerXPathNamespace('n12', $ns['nomina12']);
        }
        $otros_pagos = [];

        $otros_pagos_cfdi = $cfdi_xml->xpath('//n12:OtroPago');
        $i = 0;
        foreach ($otros_pagos_cfdi as $otro_pago_cfdi) {
            $otros_pagos[$i]["tipo_otro_pago"] = (int)$otro_pago_cfdi["TipoOtroPago"];
            $otros_pagos[$i]["clave"] = (int)$otro_pago_cfdi["Clave"];
            $otros_pagos[$i]["concepto"] = (string)$otro_pago_cfdi["Concepto"];
            $otros_pagos[$i]["importe"] = (float)$otro_pago_cfdi["Importe"];

            $i++;
        }
        return $otros_pagos;
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
        return $fecha_xml->format("Y-m-d H:i:s");
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

        if(is_string($fecha_xml))
            return "";
        else
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
                event(new IncidenciaCI(
                    ["id_tipo_incidencia" => 3,
                        "rfc" => $this->arreglo_cfdi["emisor"]["rfc"],
                        "empresa" => $this->arreglo_cfdi["emisor"]["nombre"],
                        "mensaje" => $respuesta->CodigoEstatus . ' ' . $respuesta->Estado,
                        "xml" => $xml
                    ]
                ));
                abort(500, "Aviso SAT:\nError al encontrar el comprobante: " . $respuesta->Estado);
            }

            if ($respuesta->CodigoEstatus == "N - 601: La expresión impresa proporcionada no es válida.") {
                event(new IncidenciaCI(
                    ["id_tipo_incidencia" => 13,
                        "rfc" => $this->arreglo_cfdi["emisor"]["rfc"],
                        "empresa" => $this->arreglo_cfdi["emisor"]["nombre"],
                        "mensaje" => $respuesta->CodigoEstatus . ' ' . $respuesta->Estado,
                        "xml" => $xml
                    ]
                ));
                abort(500, "Aviso SAT:\nError en la validación de la estructura del comprobante: " . $respuesta->CodigoEstatus . ' Estado: ' . $respuesta->Estado);
            }

            if ($respuesta->Estado == 'Cancelado') {

                event(new IncidenciaCI(
                    ["id_tipo_incidencia" => 18,
                        "rfc" => $this->arreglo_cfdi["emisor"]["rfc"],
                        "empresa" => $this->arreglo_cfdi["emisor"]["nombre"],
                        "mensaje" => $respuesta->CodigoEstatus . ' ' . $respuesta->Estado . ' ' . $respuesta->EstatusCancelacion,
                        "xml" => $xml
                    ]
                ));
                abort(500, "Aviso SAT:\nError el comprobante se encuentra: " . $respuesta->Estado);
            }

            if ($respuesta->EstatusCancelacion != [] && $respuesta->EstatusCancelacion == 'En proceso') {
                event(new IncidenciaCI(
                    ["id_tipo_incidencia" => 18,
                        "rfc" => $this->arreglo_cfdi["emisor"]["rfc"],
                        "empresa" => $this->arreglo_cfdi["emisor"]["nombre"],
                        "mensaje" => $respuesta->CodigoEstatus . ' ' . $respuesta->Estado . ' ' . $respuesta->EstatusCancelacion,
                        "xml" => $xml
                    ]
                ));
                abort(500, "Aviso SAT:\nError en la validación del comprobante: " . $respuesta->EstatusCancelacion);
            }
        }
    }

    public function validaVigente()
    {
        $respuesta = $this->getValidacionCFDI33($this->arreglo_cfdi);
        $env_servicio = config('app.env_variables.SERVICIO_CFDI_ENV');

        if ($env_servicio === "production") {
            if ($respuesta->Estado == 'Cancelado') {
                return false;
            }
            return true;
        }
    }
}
