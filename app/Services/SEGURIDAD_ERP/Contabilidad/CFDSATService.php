<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 05:14 PM
 */

namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT as Model;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\CFDSATRepository as Repository;
use Illuminate\Support\Facades\Storage;
use Chumper\Zipper\Zipper;
use DateTime;
use App\Utils\Files;

class CFDSATService
{
    /**
     * @var Repository
     */
    protected $repository;
    protected $arreglo_factura;
    protected $log;
    protected $carga;

    public function __construct(Model $model)
    {
        $this->repository = new Repository($model);
        $this->log["nombre_archivo_zip"] = "";
        $this->log["archivos_leidos"] = 0;
        $this->log["archivos_cargados"] = 0;
        $this->log["archivos_no_cargados"] = 0;
        $this->log["archivos_preexistentes"] = 0;
        $this->log["archivos_receptor_no_valido"] = 0;
        $this->log["receptores_no_validos"] = [];
        $this->log["proveedores_preexistentes"] = 0;
        $this->log["proveedores_nuevos"] = 0;
        $this->log["archivos_no_cargados_error_app"] = 0;
        $this->log["errores"] = [];
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    private function store()
    {
        $transaccion_cfd = $this->repository->registrar($this->arreglo_factura);
        return $transaccion_cfd;
    }

    public function storeZIPCFD($nombre_archivo, $archivo_zip)
    {
        $this->carga = $this->repository->iniciaCarga($nombre_archivo);
        $this->arreglo_factura["id_carga_cfd_sat"] = $this->carga->id;
        $this->log["nombre_archivo_zip"] = $nombre_archivo;
        $paths = $this->generaDirectorios();
        $exp = explode("base64,", $archivo_zip);
        $data = base64_decode($exp[1]);
        $file = public_path($paths["path_zip"]);
        file_put_contents($file, $data);
        $this->extraeZIP($paths["path_zip"],$paths["path_xml"]);
        $this->procesaCFD($paths["path_xml"]);
        $this->log["fecha_hora_fin"] = date("Y-m-d H:i:s");
        $this->carga->update($this->log);
        return $this->carga;
    }

    private function extraeZIP($ruta_origen, $ruta_destino)
    {
        try{
            $zipper = new Zipper;
            $zipper->make(public_path($ruta_origen))->extractTo(public_path($ruta_destino));
        }catch (\Exception $e){
            abort(500, "Hubo un error al extraer el archivo zip proporcionado. Ruta Origen: ".$ruta_origen . 'Ruta Destino: '.$ruta_destino.' Ln.' . $e->getLine() . ' ' . $e->getMessage());
        }
        $zipper->delete();
    }

    private function generaDirectorios()
    {
        $nombre = date("Ymdhis");
        $nombre_zip = $nombre . ".zip";
        $dir_zip = "uploads/contabilidad/cfd/zip/";
        $dir_xml = "uploads/contabilidad/cfd/xml/";
        $path_xml = $dir_xml . $nombre."/";
        $path_zip = $dir_zip . $nombre_zip;
        if (!file_exists($dir_zip) && !is_dir($dir_zip)) {
            mkdir($dir_zip, 777, true);
        }
        if (!file_exists($dir_xml) && !is_dir($dir_xml)) {
            mkdir($dir_xml, 777, true);
        }
        return ["path_zip" => $path_zip, "path_xml" => $path_xml, "dir_xml" => $dir_xml];
    }

    public function reprocesaCFDObtenerTipo()
    {
        ini_set('max_execution_time', '7200') ;
        ini_set('memory_limit', -1) ;
        $cantidad = CFDSAT::count();
        $take = 1000;

        for($i = 0; $i<=($cantidad+1000); $i+$take){
            //dd($i, $cantidad, $take);
            $cfd = CFDSAT::skip($i)->take($take)->get();
            //dd(count($cfd));
            foreach($cfd as $rcfd)
            {
                $xml = base64_decode($rcfd->xml_file);
                $factura_xml = new \SimpleXMLElement($xml);
                if ((string)$factura_xml["version"] == "3.2") {
                    $this->arreglo_factura["version"] = (string)$factura_xml["version"];
                    $this->setArreglo32($factura_xml);
                } else if ($factura_xml["Version"] == "3.3") {
                    $this->arreglo_factura["version"] = (string)$factura_xml["Version"];
                    $this->setArreglo33($factura_xml);
                }
                $rcfd->tipo_comprobante = $this->arreglo_factura["tipo_comprobante"];
                $rcfd->save();
            }
            if($i>5000){
                break;
            }
        }
    }

    public function procesaDirectorioZIPCFD()
    {
        ini_set('max_execution_time', '7200') ;
        $path_destino = "uploads/contabilidad/cfd/xml/zcfd_".date("Ymdhis")."/";
        $this->carga = $this->repository->iniciaCarga("inicial");
        $this->arreglo_factura["id_carga_cfd_sat"] = $this->carga->id;

        $path = "uploads/contabilidad/zip_cfd/";
        $dir = opendir(public_path($path));
        while ($current = readdir($dir)) {
            if($current != "." && $current != ".."){
                if(is_dir($path.$current)){
                    $this->procesaCFD($path.$current."/");
                } else {
                    if (strpos($current,".zip")) {
                        $this->log["nombre_archivo_zip"] = $current;
                        /*if (!file_exists($path_destino) && !is_dir($path_destino)) {
                            mkdir($path_destino, 777, true);
                        }*/
                        $this->extraeZIP($path.$current,$path_destino);
                        $this->procesaCFD($path_destino);
                    }
                }
            }
        }
        $this->log["fecha_hora_fin"] = date("Y-m-d H:i:s");
        $this->carga->update($this->log);
        return $this->carga;
    }

    private function procesaCFD($path)
    {
        $dir = opendir($path);
        while ($current = readdir($dir)) {
            if($current != "." && $current != ".."){
                if(is_dir($path.$current)){
                    $this->procesaCFD($path.$current."/");
                } else {
                    if (strpos($current,".xml")) {
                        $this->log["archivos_leidos"]+=1;
                        $ruta_archivo = $path . "/" . $current;
                        $contenido_archivo_xml = file_get_contents($ruta_archivo);
                        $this->setArregloFactura($ruta_archivo);
                        if(key_exists("uuid",$this->arreglo_factura)){
                            if (!$this->repository->validaExistencia($this->arreglo_factura["uuid"]) ) {
                                if($this->arreglo_factura["id_empresa_sat"] > 0){
                                    $this->arreglo_factura["xml_file"] = $this->repository->getArchivoSQL(base64_encode($contenido_archivo_xml));
                                    if ($this->store()) {
                                        Storage::disk('xml_sat')->put($current, fopen($ruta_archivo, "r"));
                                        unlink($ruta_archivo);
                                        $this->log["archivos_cargados"]+=1;
                                    }
                                }else {
                                    $this->log["archivos_no_cargados"] += 1;
                                    $this->log["archivos_receptor_no_valido"] += 1;
                                    $this->log["receptores_no_validos"][]= $this->arreglo_factura["receptor"];
                                }
                            } else {
                                $this->log["archivos_preexistentes"]+=1;
                                $this->log["archivos_no_cargados"] += 1;
                                unlink($ruta_archivo);
                            }
                        }
                        else{
                            abort(500,"no hay uuid".$current);
                        }
                    }
                }
            }
        }

        $contenido = @scandir($path);

        if(count($contenido)<=2)
        {
            closedir($dir);
            rmdir($path);
        }
    }

    private function setArregloFactura($archivo_xml)
    {
        $this->arreglo_factura = [];
        $this->arreglo_factura["id_carga_cfd_sat"] = $this->carga->id;
        try {
            libxml_use_internal_errors(true);
            $factura_xml = simplexml_load_file($archivo_xml);

        } catch (\Exception $e) {
            abort(500, "Hubo un error al leer el archivo XML proporcionado. " . ' Ln.' . $e->getLine() . ' ' . $e->getMessage());
        }
        //$factura_simple_xml = new \SimpleXMLElement(file_get_contents($archivo_xml));
        if ((string)$factura_xml["version"] == "3.2") {
            $this->arreglo_factura["version"] = (string)$factura_xml["version"];
            $this->setArreglo32($factura_xml);
        } else if ($factura_xml["Version"] == "3.3") {
            $this->arreglo_factura["version"] = (string)$factura_xml["Version"];
            $this->setArreglo33($factura_xml);
        }
    }
    private function getFecha(string $fecha)
    {
        $fecha_xml = DateTime::createFromFormat('Y-m-d\TH:i:s', $fecha);
        if(!$fecha_xml) {
            $fecha_xml = DateTime::createFromFormat('Y-m-d\TH:i:s.u', $fecha);
            if (!$fecha_xml) {
                $fecha_xml = substr($fecha,0,19);
            }
        }
        return $fecha_xml;
    }

    private function setArreglo33($factura_xml)
    {
        try {
            $this->arreglo_factura["descuento"] = null;
            $this->arreglo_factura["total"] = (float)$factura_xml["Total"];
            $this->arreglo_factura["tipo_comprobante"]  = strtoupper(substr((string)$factura_xml["TipoDeComprobante"],0,1));
            $this->arreglo_factura["serie"] = (string)$factura_xml["Serie"];
            $this->arreglo_factura["folio"] = (string)$factura_xml["Folio"];
            $this->arreglo_factura["fecha"] = $this->getFecha((string)$factura_xml["Fecha"]);
            $this->arreglo_factura["version"] = (string)$factura_xml["Version"];
            $this->arreglo_factura["moneda"] = (string)$factura_xml["Moneda"];
            $emisor = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Emisor')[0];
            $this->arreglo_factura["emisor"]["rfc"] = (string)$emisor["Rfc"][0];
            $this->arreglo_factura["emisor"]["razon_social"] = (string)$emisor["Nombre"][0];
            $this->arreglo_factura["emisor"]["regimen_fiscal"] = (string)$emisor["RegimenFiscal"][0];
            $this->arreglo_factura["rfc_emisor"] = $this->arreglo_factura["emisor"]["rfc"];
            $receptor = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Receptor')[0];
            $this->arreglo_factura["receptor"]["rfc"] = (string)$receptor["Rfc"][0];
            $this->arreglo_factura["receptor"]["nombre"] = (string)$receptor["Nombre"][0];
            $this->arreglo_factura["rfc_receptor"] = $this->arreglo_factura["receptor"]["rfc"];
        } catch (\Exception $e) {
            //abort(500, "Hubo un error al generar arreglo para CFD versión 3.3. Ln." . $e->getLine() . ' Msg. ' . $e->getMessage());
            $this->log["archivos_no_cargados_error_app"] +=1;
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
                    $this->arreglo_factura["traslados"][$i]["impuesto"] = (float)$traslado["Impuesto"];
                    $this->arreglo_factura["traslados"][$i]["tipo_factor"] = (string)$traslado["TipoFactor"];
                    $this->arreglo_factura["traslados"][$i]["tasa_o_cuota"] = (float)$traslado["TasaOCuota"];
                    $this->arreglo_factura["traslados"][$i]["importe"] = (float)$traslado["Importe"];
                    $i++;
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
                $traslados_concepto = $factura_xml->xpath("/cfdi:Comprobante/cfdi:Conceptos/cfdi:Concepto[" . $i . "]/cfdi:Impuestos/cfdi:Traslados/cfdi:Traslado");
                $itc = 0;
                foreach ($traslados_concepto as $traslado_concepto) {
                    $this->arreglo_factura["conceptos"][$i]["traslados"][$itc]["base"] = (float)$traslado_concepto["Base"];
                    $itc++;
                }
                $i++;
            }

        } catch (\Exception $e) {
            //abort(500, "Hubo un error al generar arreglo para CFD versión 3.3. Ln." . $e->getLine() . ' Msg. ' . $e->getMessage());
            $this->log["archivos_no_cargados_error_app"] +=1;
        }

        try {
            if(key_exists("cfdi",$ns)){
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
            //abort(500, "Hubo un error al generar arreglo para CFD versión 3.3. Ln." . $e->getLine() . ' Msg. ' . $e->getMessage());
            $this->log["archivos_no_cargados_error_app"] +=1;
        }
        $this->arreglo_factura["subtotal"] = $this->arreglo_factura["total"] - $this->arreglo_factura["total_impuestos_trasladados"];
        $this->arreglo_factura["id_empresa_sat"] = $this->repository->getIdEmpresa($this->arreglo_factura["receptor"]);
        $proveedor = $this->repository->getProveedorSAT($this->arreglo_factura["emisor"], $this->arreglo_factura["id_empresa_sat"]);
        $this->arreglo_factura["id_proveedor_sat"] = $proveedor["id_proveedor"];

        if($proveedor["nuevo"]>0){
            $this->log["proveedores_nuevos"] += 1;
        }
    }

    private function setArreglo32($factura_xml)
    {
        $this->arreglo_factura["subtotal"] = (float)$factura_xml["subTotal"];
        $this->arreglo_factura["tipo_comprobante"]  = strtoupper(substr((string)$factura_xml["tipoDeComprobante"],0,1));
        $this->arreglo_factura["descuento"] = (float)$factura_xml["descuento"];
        $this->arreglo_factura["total"] = (float)$factura_xml["total"];
        $this->arreglo_factura["serie"] = (string)$factura_xml["serie"];
        $this->arreglo_factura["folio"] = (string)$factura_xml["folio"];
        $this->arreglo_factura["fecha"] = $this->getFecha((string)$factura_xml["fecha"]);

        $ns = $factura_xml->getNamespaces(true);
        $factura_xml->registerXPathNamespace('t', $ns['tfd']);
        $complemento = $factura_xml->xpath('//t:TimbreFiscalDigital')[0];
        $uuid = (string)$complemento["UUID"][0];
        $this->arreglo_factura["uuid"] = $uuid;

        try{
            $emisor_arr = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Emisor');
            if($emisor_arr) {
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
        }catch (\Exception $e) {
            //abort(500, "Hubo un error al leer el emisor del comprobante: ".$uuid." mensaje:" . $e->getMessage());
            $this->log["archivos_no_cargados_error_app"] +=1;
        }

        try{
            $receptor_arr = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Receptor');
            if($receptor_arr){
                if(key_exists(0,$receptor_arr)){
                    $receptor = $receptor_arr[0];
                }else{
                    $receptor = $factura_xml->Receptor;
                }
            }else{
                $receptor = $factura_xml->Receptor;
            }

            $this->arreglo_factura["receptor"]["rfc"] = (string)$receptor["rfc"][0];
            $this->arreglo_factura["rfc_receptor"] = $this->arreglo_factura["receptor"]["rfc"];
            $this->arreglo_factura["receptor"]["nombre"] = (string)$receptor["nombre"][0];
        }catch (\Exception $e) {
            //abort(500, "Hubo un error al leer el receptor del comprobante: ".$uuid." mensaje:" . $e->getMessage());
            $this->log["archivos_no_cargados_error_app"] +=1;
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
            $this->log["archivos_no_cargados_error_app"] +=1;
        }
        try {
            if(key_exists("cfdi",$ns)){
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
            abort(500, "Hubo un error al leer la ruta de complemento: " . $e->getMessage());
            $this->log["archivos_no_cargados_error_app"] +=1;
        }
        $this->arreglo_factura["id_empresa_sat"] = $this->repository->getIdEmpresa($this->arreglo_factura["receptor"]);
        $proveedor = $this->repository->getProveedorSAT($this->arreglo_factura["emisor"], $this->arreglo_factura["id_empresa_sat"]);
        $this->arreglo_factura["id_proveedor_sat"] = $proveedor["id_proveedor"];
        if($proveedor["nuevo"]>0){
            $this->log["proveedores_nuevos"] += 1;
        }
    }

    public function obtenerInformeEmpresaMes(){
        return $this->repository->getInformeEmpresaMes();
    }

    public function getContenidoDirectorio()
    {
        $path = "uploads/contabilidad/zip_cfd/";
        $contenido = Files::getFiles($path);
        return $contenido;
    }

}