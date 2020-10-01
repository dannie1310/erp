<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 05:14 PM
 */

namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Events\CambioEFOS;
use App\Events\FinalizaCargaCFD;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT as Model;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\PDF\Fiscal\InformeCFDICompleto;
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
    protected $arreglos_factura;
    protected $log;
    protected $carga;

    public function __construct(Model $model)
    {
        $this->arreglos_factura = [];
        $this->repository = new Repository($model);
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

    private function store($arreglo_factura = null)
    {
        if($arreglo_factura){
            $transaccion_cfd = $this->repository->registrar($arreglo_factura);
        } else {
            $transaccion_cfd = $this->repository->registrar($this->arreglo_factura);
        }

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
        $this->extraeZIP($paths["path_zip"], $paths["path_xml"]);
        $this->procesaCFD($paths["path_xml"]);
        $this->log["fecha_hora_fin"] = date("Y-m-d H:i:s");
        $this->carga->update($this->log);
        return $this->carga;
    }

    private function extraeZIP($ruta_origen, $ruta_destino)
    {
        try {
            $zipper = new Zipper;
            $zipper->make(public_path($ruta_origen))->extractTo(public_path($ruta_destino));
        } catch (\Exception $e) {
            abort(500, "Hubo un error al extraer el archivo zip proporcionado. Ruta Origen: " . $ruta_origen . 'Ruta Destino: ' . $ruta_destino . ' Ln.' . $e->getLine() . ' ' . $e->getMessage());
        }
        $zipper->delete();
    }

    private function generaDirectorios()
    {
        $nombre = date("Ymdhis");
        $nombre_zip = $nombre . ".zip";
        $dir_zip = "uploads/contabilidad/cfd/zip/";
        $dir_xml = "uploads/contabilidad/cfd/xml/";
        $path_xml = $dir_xml . $nombre . "/";
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
        ini_set('max_execution_time', '7200');
        ini_set('memory_limit', -1);
        $cantidad = CFDSAT::count();
        $take = 1000;

        for ($i = 0; $i <= ($cantidad + 1000); $i + $take) {
            //dd($i, $cantidad, $take);
            $cfd = CFDSAT::skip($i)->take($take)->get();
            //dd(count($cfd));
            foreach ($cfd as $rcfd) {
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
            if ($i > 5000) {
                break;
            }
        }
    }

    public function procesaDirectorioZIPCFD()
    {
        ini_set('max_execution_time', '7200');
        $this->carga = $this->repository->iniciaCarga("inicial");
        $this->arreglo_factura["id_carga_cfd_sat"] = $this->carga->id;

        $path = "uploads/contabilidad/zip_cfd/";
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
                        $contenido_archivo_xml = file_get_contents($ruta_archivo);
                        $resultado = $this->setArregloFactura($ruta_archivo);
                        if ($resultado == 0) {
                            Storage::disk('xml_errores')->put($this->carga->id . '/error_app/' . $current, fopen($ruta_archivo, "r"));
                            unlink($ruta_archivo);
                        } else {
                            if (key_exists("uuid", $this->arreglo_factura)) {
                                if (!$this->repository->validaExistencia($this->arreglo_factura["uuid"])) {
                                    if ($this->arreglo_factura["id_empresa_sat"] > 0) {
                                        $this->arreglo_factura["xml_file"] = $this->repository->getArchivoSQL(base64_encode($contenido_archivo_xml));
                                        if ($this->store()) {
                                            Storage::disk('xml_sat')->put($this->carga->id .'/'.$this->arreglo_factura["rfc_receptor"]. '/' . $current, fopen($ruta_archivo, "r"));
                                            unlink($ruta_archivo);
                                            $this->log["archivos_cargados"] += 1;
                                            $this->log["cfd_cargados"] += 1;
                                        }
                                    } else {
                                        $this->log["cfd_no_cargados"] += 1;
                                        $this->log["archivos_no_cargados"] += 1;
                                        $this->log["archivos_receptor_no_valido"] += 1;
                                        $this->log["receptores_no_validos"][] = $this->arreglo_factura["receptor"];
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
                    } else if (strpos($current, ".txt")) {
                        $contenido_archivo_txt = file_get_contents($ruta_archivo);
                        $resultado = $this->setArreglosFacturas($ruta_archivo);
                        if ($resultado == 0) {
                            Storage::disk('xml_errores')->put($this->carga->id . '/error_app/' . $current, fopen($ruta_archivo, "r"));
                            unlink($ruta_archivo);
                            $this->log["archivos_no_cargados"] += 1;
                            $this->log["archivos_corruptos"] += 1;

                        } else {
                            $this->log["archivos_cargados"] += 1;
                            Storage::disk('xml_sat')->put($this->carga->id .'/'.$this->arreglos_factura[0]["rfc_receptor"]. '/' . $current, fopen($ruta_archivo, "r"));
                            unlink($ruta_archivo);
                            foreach ($this->arreglos_factura as $arreglo_factura){
                                if (key_exists("uuid", $arreglo_factura)) {
                                    if (!$this->repository->validaExistencia($arreglo_factura["uuid"])) {
                                        if ($arreglo_factura["id_empresa_sat"] > 0) {
                                            if ($this->store($arreglo_factura)) {
                                                $this->log["cfd_cargados"] += 1;
                                            }
                                        } else {
                                            $this->log["cfd_no_cargados"] += 1;
                                            $this->log["cfd_receptor_no_valido"] += 1;
                                            $this->log["receptores_no_validos"][] = $arreglo_factura["receptor"];
                                        }
                                    } else {
                                        $this->log["cfd_preexistentes"] += 1;
                                        $this->log["cfd_no_cargados"] += 1;
                                    }
                                }
                            }
                        }
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

        if (count($contenido) <= 2 && $path != "uploads/contabilidad/zip_cfd/") {
            closedir($dir);
            rmdir($path);
        }
    }

    private function setArreglosFacturas($archivo_txt){
        $this->arreglos_factura = [];
        try{
            $myfile = fopen($archivo_txt, "r");
        } catch (\Exception $e) {
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }

        $linea = 0;
        $i = 0;
        while(!feof($myfile)) {
            $linea_archivo = fgets($myfile);
            $renglon = explode("~", $linea_archivo);
            if(key_exists(1, $renglon) &&  $renglon[0] != "Uuid"){

                if(substr($renglon[count($renglon)-1], -2) != "" && substr($renglon[count($renglon)-1], -2) != "\r\n"){
                    $renglon[count($renglon)-1] = str_replace(["\n", '"'],"",$renglon[count($renglon)-1]);
                    $fin = false;
                    while(!$fin){
                        $add = explode("~",fgets($myfile));
                        $renglon[count($renglon)-1] .= " ".$add[0];
                        array_shift($add);
                        $renglon = array_merge($renglon , $add);
                        $fin = substr($renglon[count($renglon)-1], -2) == "\r\n";
                    }
                }
                $this->arreglos_factura[$i]["id_carga_cfd_sat"] = $this->carga->id;
                $this->arreglos_factura[$i]["version"] = "txt";
                $this->arreglos_factura[$i]["uuid"] = $renglon[0];
                $this->arreglos_factura[$i]["rfc_emisor"] = $renglon[1];
                $this->arreglos_factura[$i]["rfc_receptor"] = $renglon[3];
                $this->arreglos_factura[$i]["fecha"] = $renglon[6].".000";
                $this->arreglos_factura[$i]["total"] = $renglon[8];
                $this->arreglos_factura[$i]["subtotal"] = 100 * $renglon[8] /116;
                $this->arreglos_factura[$i]["importe_iva"] = 16 * $renglon[8] /116;
                $this->arreglos_factura[$i]["tipo_comprobante"] = $renglon[9];
                $this->arreglos_factura[$i]["estado_txt"] = $renglon[10];
                $this->arreglos_factura[$i]["fecha_cancelacion"] = ($renglon[11]!="\r\n")?$renglon[11].".000":null;

                $this->arreglos_factura[$i]["emisor"]["rfc"] = (string)$renglon[1];
                $this->arreglos_factura[$i]["emisor"]["razon_social"] = (string)$renglon[2];
                $this->arreglos_factura[$i]["receptor"]["rfc"] = (string)$renglon[3];
                $this->arreglos_factura[$i]["receptor"]["nombre"] = (string)$renglon[4];

                $this->arreglos_factura[$i]["id_empresa_sat"] = $this->repository->getIdEmpresa($this->arreglos_factura[$i]["receptor"]);
                $proveedor = $this->repository->getProveedorSAT($this->arreglos_factura[$i]["emisor"], $this->arreglos_factura[$i]["id_empresa_sat"]);
                $this->arreglos_factura[$i]["id_empresa_sat"] = $this->repository->getIdEmpresa($this->arreglos_factura[$i]["receptor"]);
                $this->arreglos_factura[$i]["id_proveedor_sat"] = $proveedor["id_proveedor"];

                if ($proveedor["nuevo"] > 0) {
                    $this->log["proveedores_nuevos"] += 1;
                }
                $i++;
            }
            $linea++;

        }
        return 1;
    }

    private function setArregloFactura($archivo_xml)
    {
        $this->arreglo_factura = [];
        $this->arreglo_factura["id_carga_cfd_sat"] = $this->carga->id;
        try {
            libxml_use_internal_errors(true);
            $factura_xml = simplexml_load_file($archivo_xml);

        } catch (\Exception $e) {
            //abort(500, "Hubo un error al leer el archivo XML proporcionado. " . ' Ln.' . $e->getLine() . ' ' . $e->getMessage());
            $this->log["archivos_no_cargados_error_app"] += 1;
            $this->log["cfd_no_cargados_error_app"] += 1;
            return 0;
        }
        //$factura_simple_xml = new \SimpleXMLElement(file_get_contents($archivo_xml));
        if ((string)$factura_xml["version"] == "3.2") {
            $this->arreglo_factura["version"] = (string)$factura_xml["version"];
            return $this->setArreglo32($factura_xml);
        } else if ($factura_xml["Version"] == "3.3") {
            $this->arreglo_factura["version"] = (string)$factura_xml["Version"];
            return $this->setArreglo33($factura_xml);
        }
        return 1;
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

    private function setArreglo33($factura_xml)
    {
        try {
            $this->arreglo_factura["descuento"] = null;
            $this->arreglo_factura["total"] = (float)$factura_xml["Total"];
            $this->arreglo_factura["tipo_comprobante"] = strtoupper(substr((string)$factura_xml["TipoDeComprobante"], 0, 1));
            $this->arreglo_factura["serie"] = (string)$factura_xml["Serie"];
            $this->arreglo_factura["folio"] = (string)$factura_xml["Folio"];
            $this->arreglo_factura["fecha"] = $this->getFecha((string)$factura_xml["Fecha"]);
            $this->arreglo_factura["version"] = (string)$factura_xml["Version"];
            $this->arreglo_factura["moneda"] = (string)$factura_xml["Moneda"];
            $this->arreglo_factura["tipo_cambio"] = (float)$factura_xml["TipoCambio"];
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
        $this->arreglo_factura["subtotal"] = $this->arreglo_factura["total"] - $this->arreglo_factura["total_impuestos_trasladados"];
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

    public function obtenerInformeEmpresaMes()
    {
        return $this->repository->getInformeEmpresaMes();
    }

    public function obtenerInformeCompleto()
    {
        return $this->repository->getInformeCompleto();
    }

    public function obtenerInformeCompletoPDF()
    {
        $informe = $this->obtenerInformeCompleto();
        $pdf = new InformeCFDICompleto($informe);
        return $pdf->create();
    }

    public function getContenidoDirectorio()
    {
        $path = "uploads/contabilidad/zip_cfd/";
        $contenido = Files::getFiles($path);
        return $contenido;
    }

    public function generaCarpeta()
    {
        $uuid[]='00D22591-AE5F-4670-B5C7-C34BA2EA6E3B';
        $uuid[]='01265529-BBB1-4C78-9166-92DFF593DEF8';
        $uuid[]='0171D9F1-B832-4084-BB8F-FBB4E4B7BB36';
        $uuid[]='027aed59-73e3-4b2b-9880-fad473680313';
        $uuid[]='045E8EEB-47F1-4134-A5DB-07338C4F8A97';
        $uuid[]='05124015-FBF0-4815-AC07-B4C7669F3267';
        $uuid[]='05220487-EE6A-487B-A8CD-B27587AEF26F';
        $uuid[]='061278B1-652B-4E32-87B4-FCFC040E6F28';
        $uuid[]='07F019A6-90B1-5A81-7C73-082B52FF1CD6';
        $uuid[]='08AB51FA-849E-48F4-BDF9-CF0DE63E2F40';
        $uuid[]='08B6A7BB-B8CC-1E01-5620-0E345D55EC5B';
        $uuid[]='09B43561-269A-4609-8934-0AE90BE5004F';
        $uuid[]='0A01FDDD-5413-FDF9-5140-B427EAE72743';
        $uuid[]='0A20EE93-192A-4125-AE49-1DC1DEC8AD51';
        $uuid[]='0B4DA62E-6010-41CC-84BC-8D901C97E7FA';
        $uuid[]='0C6A6423-61FF-4F4A-8F16-FF5DDFF634E0';
        $uuid[]='0CEB4C3F-8150-430A-A218-B36DD3B3A887';
        $uuid[]='0DA6160A-A23B-4E30-80AD-FDD975F87068';
        $uuid[]='0F4EF82F-B505-444E-80A6-222A41A47900';
        $uuid[]='0F6F44EA-1385-3D76-EE99-C10A6778194A';
        $uuid[]='0acba67c-7f72-4b3f-a7fc-1d826a9242d9';
        $uuid[]='0d705fe7-1d59-4df6-8445-ee90d7a1d59b';
        $uuid[]='0ec3dae0-6840-4b33-b132-0f97cb8c774c';
        $uuid[]='100E85E7-3B05-44FF-B895-FD06CBEFC380';
        $uuid[]='10D10177-9518-40DB-84DC-69E195F08093';
        $uuid[]='1122CFA2-58C6-4E65-A7A2-890A8C4B929D';
        $uuid[]='1168BE52-0221-4EA3-BDB0-948C7795DB3F';
        $uuid[]='117CF4C0-53C2-4C8F-9F0B-3ED6AA581D12';
        $uuid[]='1277FA82-FE7A-27DB-76AC-104E607C04C7';
        $uuid[]='12B6EAEC-26D6-A61E-806B-3443963141DC';
        $uuid[]='12C32017-5E21-DF32-FA06-0F41D1BBE902';
        $uuid[]='12C72C51-38F0-4E8D-B46D-AE75DE3744B4';
        $uuid[]='130C361D-6AE3-497F-81A0-63D93343CF87';
        $uuid[]='13114D77-5DD3-4C0A-B58E-C451DABD4708';
        $uuid[]='132E2E2C-4159-40FB-8CB9-A6E2D08DCB3B';
        $uuid[]='13f44cd7-e3c0-4117-b523-01e8e16b78db';
        $uuid[]='14E9AD21-AA20-89C4-6CA1-43C1FD6670C7';
        $uuid[]='1548C4D3-A4A2-465E-9EE1-DD16EC512A86';
        $uuid[]='159970FE-0BEA-419D-A15A-660F5F658430';
        $uuid[]='15B2BB90-7AA4-45BF-BD71-0CCFFF5CA9BA';
        $uuid[]='163253C6-DA39-4C64-B9F2-CC70A6517F20';
        $uuid[]='163D4EB3-C859-4D4A-85CC-A294CD5FA153';
        $uuid[]='16df9cd8-6fbf-4944-96ce-b13d5141a507';
        $uuid[]='179195a5-c6fc-4fad-86e9-3f3b95f2c180';
        $uuid[]='17d380ee-defa-445a-9aea-f684ec6479ab';
        $uuid[]='1958ccc2-a3b4-499e-bae9-58f720ea0a22';
        $uuid[]='1A06709F-4C1F-5221-E869-96AB8670BFC7';
        $uuid[]='1AAD8D2C-4958-4F87-ABE6-0DDE85F6D3A3';
        $uuid[]='1AD293D3-A891-4E1B-8414-5BEAB21E142C';
        $uuid[]='1B507081-5E27-8592-C89D-68915146AC77';
        $uuid[]='1C043ACF-1DC2-3678-EF72-81883FAAD198';
        $uuid[]='1C35ED82-7D49-47DA-8A39-5B8026A05114';
        $uuid[]='1CB4EB8F-734C-466E-9E76-C1F806F11787';
        $uuid[]='1DAE503C-AB0E-4688-9620-FDC0C42B4D96';
        $uuid[]='1E3E4C42-E636-4876-9A0A-6CB6C0280CD0';
        $uuid[]='1E585971-39BE-4A41-B26D-AC83FC1E3505';
        $uuid[]='1E70B39F-F8CD-ECA9-40FF-B27566496EF6';
        $uuid[]='1a14fce3-1636-4961-8624-e78abbfc295d';
        $uuid[]='1f21b237-a3c5-42a7-9140-50daa03de7d5';
        $uuid[]='2044D5F5-1C40-44BF-A4EC-55B14CBED9AF';
        $uuid[]='2151633F-40BE-49D3-ACB8-CB8C9E3184A5';
        $uuid[]='216d76ab-ca5d-4e5f-ab01-cc96c4a145af';
        $uuid[]='21A62BAB-03A0-4F1B-96C4-7099B3B1F936';
        $uuid[]='21C1110B-7D72-467C-86B0-24F981A82AF3';
        $uuid[]='21e14a56-40d9-493e-b6ba-201d6547fcdc';
        $uuid[]='22F0A395-7052-4C04-B613-958F099410AA';
        $uuid[]='234315EA-B20B-5695-3D5E-840F5EF30F3B';
        $uuid[]='241d14ed-6eb2-422a-8858-c746b32514a7';
        $uuid[]='251D5A78-B368-A0C7-2EED-EDAA6A5E78F0';
        $uuid[]='25af08fa-bea4-4977-abae-048402a5fda3';
        $uuid[]='26FDADB5-DF59-492A-BCA7-4E1EF64450E7';
        $uuid[]='274EB688-6BD9-7A9C-C8F3-759F3248C2D5';
        $uuid[]='2770402D-42E5-4572-904A-3EA76C12B0A2';
        $uuid[]='27A2358E-9EC7-43DE-A486-DF0641F64E55';
        $uuid[]='289490ae-533e-4434-8caa-c08c3cbba1d9';
        $uuid[]='291A2004-40F7-AEAC-62F2-235707237974';
        $uuid[]='29478432-D392-49F1-988F-7755D328CC72';
        $uuid[]='298DA2EC-578B-DC0A-7148-500B72FAA692';
        $uuid[]='2995E5BC-0756-41E8-8377-35DD6945E0FD';
        $uuid[]='2AD17065-C053-261A-BCAB-824561D8E873';
        $uuid[]='2C1102AF-8F13-37A7-EE39-7BF539C849C6';
        $uuid[]='2DC4D08A-0CF5-420E-9079-ADD208E8940D';
        $uuid[]='2EE6D8AA-84D9-4286-8F83-E6B1DC06B683';
        $uuid[]='2EE80E0E-5ECF-4A9E-876D-AA7EBF0EE97B';
        $uuid[]='2FCC5340-2DB2-4481-B82A-CED2068FA5F6';
        $uuid[]='2e22c33e-7152-4a1d-adb5-f3f9ee170af0';
        $uuid[]='320AC657-4981-4AAE-8673-16C5CE0B3FED';
        $uuid[]='32440396-D05B-4E1B-8EED-07AD413F3CB0';
        $uuid[]='359C068B-CCD7-41D2-9029-A5B306E32A15';
        $uuid[]='37CCAEA2-0D88-412C-BDD6-513443CA7FB0';
        $uuid[]='3947F358-A6DB-4850-86C1-3742B46C1B88';
        $uuid[]='39709057-5D52-5394-8AE4-79CEE14AEE49';
        $uuid[]='3C98DD53-F7E3-4D4E-BB9A-D5B7BBA764EA';
        $uuid[]='3D106855-A094-45E2-8016-498C49653518';
        $uuid[]='3D99EE54-4DBE-415F-A06A-7A6203670CA5';
        $uuid[]='3DF14EAE-D420-4B05-A023-C1E2AE378449';
        $uuid[]='3E55336F-FD6D-432C-A2FC-972CA13F23F7';
        $uuid[]='3F1D5A75-4FFC-4E18-A1EC-31BCF29C954B';
        $uuid[]='3F770189-118B-4BF0-9037-D2D2FD581D17';
        $uuid[]='3dc61375-cea3-4701-ab49-95c641cfb841';
        $uuid[]='40bd3b55-da57-4908-a9da-78a9499b21f5';
        $uuid[]='410830B1-9612-4880-B613-C971AA28C2BF';
        $uuid[]='41F641E3-70E6-41BB-8B0B-4384CC323A3C';
        $uuid[]='4469CCC4-3A64-4B17-ACC3-340B5C7DAAFE';
        $uuid[]='46051BFB-61C8-40C8-87AA-E98DBD2ABBDF';
        $uuid[]='461C330B-60A3-ABF7-48E0-1956D0DD6599';
        $uuid[]='4675F52B-F5CF-AAED-F94F-10A23BD0B740';
        $uuid[]='4738AF38-5B6E-4C42-A90B-47E8D666A658';
        $uuid[]='47ADE98D-EACC-47B5-9E56-A62C92ED2D6C';
        $uuid[]='4812CD3B-C784-4DD0-B410-89DBE2DCC4AF';
        $uuid[]='4837458D-1246-6445-CC2D-7D1A257D7232';
        $uuid[]='48EEF0C7-16C2-3133-FF18-6660C6635B08';
        $uuid[]='4922418F-EAE9-9054-E009-BE0AC8E79ABD';
        $uuid[]='4991371E-F8B8-5DA5-5175-8E126FD9BFEE';
        $uuid[]='49C8598D-3EEE-40FC-A90B-EA381673EDCE';
        $uuid[]='4A41FFC0-7B22-41B0-829E-C72F0BA45519';
        $uuid[]='4AB125AC-0CCC-46A9-9178-B1E18527A519';
        $uuid[]='4C00F274-9F45-42EB-8DC1-B52A705192B1';
        $uuid[]='4C5F56B9-FC1B-F18C-0F3E-46C2F8A66746';
        $uuid[]='4CF7E6C1-5078-1FEF-2DCD-3EBF0388D01E';
        $uuid[]='4DD73970-6898-4580-BDF6-4F1BE100CEF8';
        $uuid[]='4E386AAB-ADA7-4BFD-8302-E0C6B9821E35';
        $uuid[]='4EA60EFA-8E7D-40F6-AE7F-B4BD4540DF3E';
        $uuid[]='4EDAFE84-054F-4A1F-9415-6B9E82EDAA61';
        $uuid[]='502562ce-a90f-4aa0-869a-7344ed192b27';
        $uuid[]='5085FC9B-1414-640C-B042-A78F1DB03397';
        $uuid[]='50B0D2EA-8976-D0D5-8143-FBBDBF068012';
        $uuid[]='5138A6CB-150C-A5B8-3330-66181FF209F2';
        $uuid[]='519b2196-684f-43d7-a87b-b0c7554c9f1e';
        $uuid[]='51AF3026-5DEA-43E5-83B0-F7564350B5A4';
        $uuid[]='52C39579-38FD-48BA-11A3-ED59EB87B7DD';
        $uuid[]='53381A03-3E46-4B27-B979-95336E9802ED';
        $uuid[]='535583F9-0B53-4C87-B849-C59A7D3B734B';
        $uuid[]='54511FE4-DDE8-40D6-BE79-F0D844161707';
        $uuid[]='54560637-1039-4f5a-ae54-2c8a754d6d54';
        $uuid[]='5665B9D8-7F94-4FD4-A015-8BB5517FBE06';
        $uuid[]='57369ABE-A454-4CB6-A815-D06AB548F87A';
        $uuid[]='57F68BBF-2E21-1AD6-D484-B3E7F42AFC7E';
        $uuid[]='58742819-C7CF-49E4-AEB6-FEDBF15D7379';
        $uuid[]='5939F3FB-639F-439D-BBF1-2CABBAF602B5';
        $uuid[]='59c312cb-25c0-4cae-8467-b9fe2ba9489c';
        $uuid[]='5AFEE660-58AF-4503-B3AD-63A48E472B94';
        $uuid[]='5B0F2DFC-E55A-4493-A50C-1D60338D1489';
        $uuid[]='5B40CAE5-57CB-47F8-8F0C-777AAF146A16';
        $uuid[]='5B6159D3-BCE6-4D8A-8AB7-AF50BFA4414D';
        $uuid[]='5BE4E424-9DB7-43F1-BF1B-9FF9A7B727EC';
        $uuid[]='5C80AFF1-4101-43AB-835B-BFB352CDDAF9';
        $uuid[]='5E9E6171-12DA-45E8-8986-2A13DC2F3215';
        $uuid[]='5F82651F-AC34-42B1-889E-979F1B1BEB08';
        $uuid[]='5FBFCD67-C9F2-49B6-8AC8-9D22BF83E62D';
        $uuid[]='5a537ee8-bda2-447a-9307-34e20255a2c5';
        $uuid[]='5f1096ab-5196-4509-851b-ac91a1152b58';
        $uuid[]='5f5d7448-b73f-483d-9459-b4c1002ec7e3';
        $uuid[]='60412FCA-AC52-48E6-B0ED-16DFF082D122';
        $uuid[]='60A962DD-F5B9-403A-A7CA-17E566933D6D';
        $uuid[]='60E33C25-D6D3-4BF6-80BA-1EF046265748';
        $uuid[]='61124EC2-203E-4CB6-8CDD-32BD4A3C8E9C';
        $uuid[]='6127CBA9-BA85-4FAF-959A-984B6E039411';
        $uuid[]='62258AEA-059A-41CD-B8A8-D1A24EE162BE';
        $uuid[]='629bbecc-94b4-4784-9720-16735243dd15';
        $uuid[]='62D0174B-6D75-4786-832A-545CF0FCDD8C';
        $uuid[]='630B9208-5DC8-4327-87EC-4A0722B38C27';
        $uuid[]='63C8F4FC-8A25-5EA5-1EA7-BE206ED1E087';
        $uuid[]='63abc76b-8483-4679-aa22-facfd502fa6c';
        $uuid[]='64ea1f7e-561c-468e-bc79-bae445a11b40';
        $uuid[]='67ddc439-e426-4dcb-8dd6-2cade5285d3d';
        $uuid[]='685CB874-6603-4C73-8CC1-2E545062E5C8';
        $uuid[]='689194BD-AB86-4E8C-9D0C-DF691B4A9F00';
        $uuid[]='694AE5F7-32F5-41CB-A375-B194185EFFB9';
        $uuid[]='696dd812-3a90-4a76-8d06-93782625d92a';
        $uuid[]='6A7F6BF9-DEDF-4960-B5AB-4BF964AAC243';
        $uuid[]='6AC6CE4B-C2EB-A3A3-D648-054DF9652E50';
        $uuid[]='6AFCDE07-A933-4F34-A3B9-BA6D1123F1D3';
        $uuid[]='6B0C1092-7310-4F42-8AEC-2AD27338F1B2';
        $uuid[]='6B3CF96F-D2E7-5EC7-4C75-96D8EB1B9AC6';
        $uuid[]='6B822040-65C0-4B24-AB3C-837B8E74E553';
        $uuid[]='6CABD029-A4D4-4B4A-B273-0C5B39E8527A';
        $uuid[]='6CF84B55-0E64-45D1-8DA2-9902BF7FA800';
        $uuid[]='6D36E506-DE96-4CAB-8EAA-8A490DAC2960';
        $uuid[]='6DF78075-11D3-4986-B2F4-2F08E25EC3A1';
        $uuid[]='6E154FCA-0B10-41AA-B08C-59AE5589FDEA';
        $uuid[]='6F26B187-40CF-4438-3A58-4AE2A2404FC9';
        $uuid[]='6F4256C1-10D8-4ED5-A3FA-356EECBDD9E9';
        $uuid[]='6FA4F7B1-E9D3-530E-211E-5678AA7967A8';
        $uuid[]='6FF048C2-E492-4CF5-BBF3-2F9E028FA8AC';
        $uuid[]='6c11f82f-e532-4e7d-a432-c9b79b5805eb';
        $uuid[]='6f19bd2c-1881-4faf-94df-e315ef5fb7f1';
        $uuid[]='7023BC6A-0F8A-BA17-8A07-FC04E2C5599E';
        $uuid[]='702C00A3-9486-4DD1-B65E-93B9581DD568';
        $uuid[]='712190FB-3EAA-406C-AC70-EE158F6B16F4';
        $uuid[]='715BF3F4-2BCA-4CA0-A37E-8AE614E72F60';
        $uuid[]='716C32B5-7072-46B4-8315-97A283C4A8A7';
        $uuid[]='71d7887a-0a07-4b80-966f-6356a89c01bb';
        $uuid[]='734CFFDE-A789-995A-B7C5-5DE831C989E6';
        $uuid[]='7655D61A-F7D2-4DD7-8F20-5861440562AF';
        $uuid[]='775b9073-a0a5-416a-8def-2adf814c3a9a';
        $uuid[]='776D296E-FD9E-4B8C-92F9-30A1D425E5C0';
        $uuid[]='781CBEE4-A37B-B17B-D45B-8D722AEE7F2B';
        $uuid[]='78A70B60-0A42-8CD7-C780-15D6F0E57B53';
        $uuid[]='795BB234-8288-47F3-AB61-D52123C642C3';
        $uuid[]='7974FD0A-7CF7-430A-8496-AB0D479857FE';
        $uuid[]='79C7BC76-316C-41CD-B9D0-A72EAE772B88';
        $uuid[]='7A2A1A44-B6A9-469D-8A41-A3655C2BE152';
        $uuid[]='7A99B957-48F0-4652-BDB8-AEF2845DD14C';
        $uuid[]='7ACC263B-FD1F-DC4D-4F80-E9132882A818';
        $uuid[]='7B5EE08C-38AC-4185-B92D-1FA4DD58B05A';
        $uuid[]='7BB0A41E-8D20-DDBE-8D39-9FC404B98E55';
        $uuid[]='7C12BE55-1FB5-45AB-90C7-CC7B98787F82';
        $uuid[]='7EE3D484-7FCA-75FC-1064-CCD8372EA71D';
        $uuid[]='7FD0FFEA-D5F3-4F93-8304-8EBB5051746F';
        $uuid[]='7FD4537D-FB08-8647-BE46-865166F261F2';
        $uuid[]='7FE76779-2BB4-4493-9E6F-D53BCC729700';
        $uuid[]='7b4ef503-79bd-49f1-b106-79bd7fe60daf';
        $uuid[]='8196A66A-B7F2-4CEE-8D32-F9FD256FB87D';
        $uuid[]='83e438b3-fb82-4fa6-90ae-7730f4523bb3';
        $uuid[]='85400259-510A-4B5D-A68F-A4C96B924281';
        $uuid[]='85A98BE0-FB30-7BA6-9B2E-C53BEB0288E6';
        $uuid[]='85BDAC28-EE6A-430E-8A43-B0681B93015A';
        $uuid[]='86598811-BAF2-43B3-A5F0-4F7F4F1A5536';
        $uuid[]='86f8cbd6-8aa8-4657-869f-9d4be7f728fd';
        $uuid[]='8731647D-9DDF-45CA-84A3-BC24185A8991';
        $uuid[]='888EF52C-3E0B-4FA8-9FEB-443421999098';
        $uuid[]='88C7D537-CDB6-4C64-BCB8-0B9E9C5283A7';
        $uuid[]='891464F1-5850-4263-ABE0-838C68B93AB5';
        $uuid[]='897153bc-406b-4e42-a7c6-27a0901d0894';
        $uuid[]='89e2739f-61a9-498a-8e3f-aaebdff25df7';
        $uuid[]='8A57147C-31DE-452C-AB5A-EE847AA14581';
        $uuid[]='8A5D9400-A46B-461A-B7E5-AEA934D619C5';
        $uuid[]='8A62BCAC-5712-4622-8C3A-F7517E6B8198';
        $uuid[]='8A6A93FE-0963-4AE1-BC08-D78DD096C70E';
        $uuid[]='8ADCD633-755C-49CA-890B-E740D6849435';
        $uuid[]='8CCAD5B0-536F-40B3-A8E6-013D9A3548B0';
        $uuid[]='8CE547DD-6078-B551-CAC6-7E4B16CB03E6';
        $uuid[]='8D69ACE7-0791-4181-8B26-3145F37FEF15';
        $uuid[]='8DAC9763-0F96-4AC0-A93D-806A3CEDE469';
        $uuid[]='8E65C00F-6482-716C-4FD2-E8F2C896633B';
        $uuid[]='8E6B5F0A-CA02-4322-8B09-7BC14DD3EA92';
        $uuid[]='8F377E97-908B-008A-C957-A547B538D804';
        $uuid[]='8c9e9880-b336-48a7-8b59-aac6a193974f';
        $uuid[]='8ce61668-8a46-49eb-a94c-9f7a6a73b31b';
        $uuid[]='9083873c-92c9-4dfe-a92c-e61021779d21';
        $uuid[]='90A31604-FAFC-44EB-A953-0F116558CC60';
        $uuid[]='915C046E-EEC1-47A2-8510-F737E849DC0C';
        $uuid[]='918c5b4b-d1ac-41d2-ba6a-0431148e9d0d';
        $uuid[]='92AE7EFD-0A5E-8E41-32B3-CF7FFA18CBE8';
        $uuid[]='931D8B60-8F6D-4A48-9792-3ECC040EA78D';
        $uuid[]='9363159C-8F8B-DC62-4B5B-0B0318696490';
        $uuid[]='93cbd876-c8a6-4c0b-a4d9-90b5c83d2b98';
        $uuid[]='9409BB4E-FC3E-4016-BC03-8CD8DD88442B';
        $uuid[]='9411C90E-F4DD-4285-9739-35DECD9C70B8';
        $uuid[]='94e18dbe-2f6a-492b-9d71-2a7bb88c0343';
        $uuid[]='967AFA28-C3B3-49E6-BC64-052F06823E7B';
        $uuid[]='9756693E-3DCC-400E-A890-4F317A967E98';
        $uuid[]='987F32E0-6B00-46E5-A264-8B7BE0EF82CF';
        $uuid[]='994FA97C-36BA-4F74-B296-BA902CB364B9';
        $uuid[]='9A849B0F-06D8-4FD5-952C-BD3275BB7E07';
        $uuid[]='9AEF06A2-62A6-4EB9-B31F-3ABFC70676E9';
        $uuid[]='9B2FF976-3D81-4619-937A-0269A2249313';
        $uuid[]='9E235B4C-278F-4330-9626-36D4FF3C2632';
        $uuid[]='9F4784F5-9FAD-4028-934F-9E948DD6402A';
        $uuid[]='9F49BA4D-1C0C-43A3-97E8-935D64EC852D';
        $uuid[]='9FBE08EA-E43F-4964-9CD9-04FF5C56D4C5';
        $uuid[]='A108E0E5-64B6-A94B-A1EC-C3D3452325C4';
        $uuid[]='A192AEDD-5EE8-4FD6-A212-E928D4F51646';
        $uuid[]='A2F5D70E-0680-D304-58BA-E9F57AA94C90';
        $uuid[]='A3A64763-5E4C-C240-7D83-1C04B38CD355';
        $uuid[]='A3D6A176-8510-4DF5-BEE6-CB6CC370DC84';
        $uuid[]='A3DF1EC6-50C0-4F05-96AB-6025D6EDD2EC';
        $uuid[]='A400D474-44CD-466A-AF0F-23704FFF0313';
        $uuid[]='A43BF798-5CFB-4DE9-9047-2BD81263AB9E';
        $uuid[]='A477273E-8539-4672-981C-37A91DF79A8D';
        $uuid[]='A4A3B0D9-CB30-4BA6-A006-237A8296E350';
        $uuid[]='A5030457-7EF0-441E-A255-5A1C72D04630';
        $uuid[]='A5DFA48F-F47D-4FD0-A6E7-42B0B8E13419';
        $uuid[]='A62CB579-C70D-4153-8DC4-24A4DB1C9454';
        $uuid[]='A658F9FA-CA39-B2F1-F02B-09C60C37A024';
        $uuid[]='A6EE209F-9925-93CC-C9F6-DAD53BBDAB1D';
        $uuid[]='A75923AC-540C-46B7-8E54-1BEC62D14F08';
        $uuid[]='A7B566BC-8437-4743-D859-8A0FE2F23760';
        $uuid[]='A8566453-40B9-4380-AC10-0B73F92AB213';
        $uuid[]='A976EFCF-D524-4C66-B606-5BBBA1CCF601';
        $uuid[]='AA015971-563A-4965-BE2D-21205587A35B';
        $uuid[]='ABB23A6E-5664-E887-3E30-7E326EF90010';
        $uuid[]='AC8A0E1C-4B3E-29E1-D78D-D1B159D6F7C1';
        $uuid[]='AC995233-61F4-42AD-BF19-270E577D736A';
        $uuid[]='ACD7D0D3-A26F-410D-9963-1A8F7FC036D3';
        $uuid[]='AD43F449-49D6-401F-84C0-1FBC5BAA3BD1';
        $uuid[]='AD86D359-3E98-527D-5D5A-1A04E49442B5';
        $uuid[]='ADBC287F-D2FC-417B-B846-1A76210E323F';
        $uuid[]='AE4C1956-A7A1-4B18-A6DF-AEE6B8ECAE24';
        $uuid[]='AEB506E7-FC02-4F89-9C63-368E30761747';
        $uuid[]='AFF367B9-D5DD-42B4-8432-644BEDD95985';
        $uuid[]='B0294D87-99A6-A7FD-F310-7A1D5D78D7CD';
        $uuid[]='B1187ACD-1663-E141-8D67-5AFC0F10DFC1';
        $uuid[]='B2534619-50AD-824D-A35B-1D4AEA4DDF8B';
        $uuid[]='B2C56AE6-B977-4407-9C28-BFD67C62BB96';
        $uuid[]='B428AD9A-8319-468C-8F5F-6CD4B378F3EA';
        $uuid[]='B441912E-3F84-4103-8412-B3C7A71F786F';
        $uuid[]='B47C9BA6-11ED-4FA8-809C-D136C9177B02';
        $uuid[]='B4AA65FD-5B2F-4756-B907-42CF4338BDEF';
        $uuid[]='B4BCF55E-A6D6-4875-9B6E-5362B0BE56F7';
        $uuid[]='B4FE90C8-53AC-486E-A819-81C41B98DF13';
        $uuid[]='B5469C37-F506-4BF5-8FDD-22D8C9311163';
        $uuid[]='B6C30F55-8C12-681E-33AA-89E2EE957F56';
        $uuid[]='B7154FA9-E5F2-4BB4-9819-452C70D3A392';
        $uuid[]='B73A3E79-87E8-4A8D-B5D8-82FEE1F5BB31';
        $uuid[]='B7EDC2F8-CB2A-B4AC-9CD9-CF0C7E14E556';
        $uuid[]='B83121E6-3020-40B5-9E31-FCAC4122D001';
        $uuid[]='B861AF10-AB83-463C-B206-8B98C08E7E40';
        $uuid[]='B86A4734-3FD0-44A6-93CC-66B0EB2A6061';
        $uuid[]='B86EA684-5D42-4F38-BCD3-82E6AD05808F';
        $uuid[]='B8D673C1-D3CC-4FBB-8AB5-A60F7FFFE892';
        $uuid[]='B98104DE-1AD8-4A73-82A1-382C86754049';
        $uuid[]='B9A20492-EBF3-CA8B-EAE4-DBFC3AD2F821';
        $uuid[]='BA131C73-35D9-4093-8AAE-E0EA5B2D5EF6';
        $uuid[]='BA9E8F3E-52FE-4F1C-B053-05AC9FA4EB80';
        $uuid[]='BB354A3D-D5C8-741C-6076-4F2CF6BCE246';
        $uuid[]='BBAB9C4D-ACD3-4813-92A2-B250A7A5A15C';
        $uuid[]='BBAD1A25-83CF-DDB1-1709-C37F25EC3A70';
        $uuid[]='BBC4B872-DEB2-4451-9C56-C88C39624655';
        $uuid[]='BBD041A3-23A2-A921-5C06-652B631F6555';
        $uuid[]='BC9A65DF-2D05-A6F7-C62B-98DC608BFCBA';
        $uuid[]='BCC9AAB0-6E0A-4DD6-A10C-621731A06728';
        $uuid[]='BE237CB6-87DD-4869-99E3-6E2978359D38';
        $uuid[]='BE2DEFE3-82E6-5FD8-6869-7B9F2D9BCBBF';
        $uuid[]='BEFA1FB7-9699-1C5D-C180-A88D6B083C40';
        $uuid[]='BFF50759-A52A-48AF-B614-9773318AAC05';
        $uuid[]='C12EA46A-9A31-4EED-ADE7-5835524451F3';
        $uuid[]='C16FC993-B07E-C743-AB89-8C5061B78687';
        $uuid[]='C1C06A9B-B777-46C7-A753-9F335720EF05';
        $uuid[]='C1FE6EFE-F890-4968-A1D4-5D95C495721E';
        $uuid[]='C29EACB6-03E7-4F92-89CB-FAA7B36E8A0B';
        $uuid[]='C34C19F6-15C1-4A38-89DD-7F0B2B6291BA';
        $uuid[]='C364B0A6-FA26-3848-D4A0-DF11D5D6CE74';
        $uuid[]='C37363D7-5DEE-4F33-A546-A2892B2595C0';
        $uuid[]='C39E26D0-EAEE-4FD8-C4E2-AE4A050D6645';
        $uuid[]='C40F394A-19CC-471C-94C9-A1DB150E3885';
        $uuid[]='C4FDAB7F-454B-4F65-969F-CE7DF7C3A2B6';
        $uuid[]='C637FD7C-C95E-4E8F-A147-E9E539C6AD56';
        $uuid[]='C6B536C2-4DF6-4C01-B6D3-45E4159922F4';
        $uuid[]='C7210AE2-1FE9-8135-98C0-EB5A5CB77B57';
        $uuid[]='C72C8379-3AD9-435D-AABB-72BA0A54FBC5';
        $uuid[]='C88406AC-24F5-CDDD-6D60-703E6D054E6E';
        $uuid[]='C89E4CA7-7311-4F59-A74C-4F82F261BE0C';
        $uuid[]='C9A1200C-A15D-44F1-AB66-E80D17D48C2F';
        $uuid[]='C9EDBF03-14B2-4919-8E4E-FA88A78AC2A5';
        $uuid[]='CA4067D3-FC12-4CF5-BCCE-11EE75F810AE';
        $uuid[]='CA994EFA-CFB2-DD11-9B41-9832E482AA1A';
        $uuid[]='CAE4057D-CC20-4F23-8DCA-EBC5F75E15C7';
        $uuid[]='CBD737A4-0119-4331-95A6-53B028E3336C';
        $uuid[]='CE54D63D-7498-40F2-9B0F-9B8319726E43';
        $uuid[]='CEC3D464-1AEF-482A-918E-7219FEAC16D1';
        $uuid[]='CEE352E4-1CA7-22DA-B811-98FAC7E37EE1';
        $uuid[]='CEEBD838-1E03-5774-FA03-B9FC4180D4C6';
        $uuid[]='CEF6ABA0-E770-925F-11FC-161B05FE0890';
        $uuid[]='CF8FB881-CA65-B957-96C9-AED0FD926433';
        $uuid[]='D139ECD9-E791-473E-9EDD-362E9B1B4A0A';
        $uuid[]='D1694D80-3747-49CC-A45A-6379633CE2B1';
        $uuid[]='D25E5E9B-D0A9-40FF-BACC-9AC4EB27EB20';
        $uuid[]='D262CAEE-7D36-4624-A13D-F44B88B31AF1';
        $uuid[]='D443C0FE-A158-4ED1-89E4-0A9013051E2A';
        $uuid[]='D457C1B5-BF2F-440B-B0E6-1DC70342ED85';
        $uuid[]='D45CDEEF-C66D-4BDC-93B0-862F2D4C196D';
        $uuid[]='D4A61EBB-03E5-4EF4-9AB4-7251DC469A9F';
        $uuid[]='D605DA47-B9ED-4D28-8C93-D597B0F0A260';
        $uuid[]='D657301B-1FBE-4A03-AD76-2491F71A039B';
        $uuid[]='D65906B2-DF2F-4AB2-A2BE-CDAE4075A82E';
        $uuid[]='D84812B4-425B-107D-D529-A188B90ED9E3';
        $uuid[]='D88B8141-8980-4C24-843B-62D38B14AA88';
        $uuid[]='D8DE2F48-E2B0-486D-B5D5-5707D9C8E4C0';
        $uuid[]='DAA0F76F-3F4B-4DC8-B328-AB789AF06E3C';
        $uuid[]='DAA2BCF0-8527-D040-990F-4ABBB9F4C686';
        $uuid[]='DC2E1C19-3B5C-EFD4-7CB2-6535581BD511';
        $uuid[]='DC5FE48B-7390-4B1A-93AA-2A2DF86F4633';
        $uuid[]='DC9F4B39-2CD1-45C9-9A8B-92B7B268373C';
        $uuid[]='DD79258F-EDF8-1A7F-75C2-6235D3CD37E4';
        $uuid[]='DD7A935A-8691-42F1-917E-46C2F27BAEF2';
        $uuid[]='DDA494CD-B527-4BDB-B6C8-81572F730637';
        $uuid[]='DDAFC275-C0F5-BE7B-A82A-ECA5CDCE1C0F';
        $uuid[]='DDCFCDF9-B38A-4D36-B93A-BDD95A99E8E8';
        $uuid[]='DDE827E6-DF4B-4791-B4C9-358930B81AF4';
        $uuid[]='DE3A36C5-FE51-B30E-56E0-93BCA72193D9';
        $uuid[]='DE6724C8-C257-44CC-AD76-5AC615413D60';
        $uuid[]='DEF42E37-2F77-4A6B-BC67-8B2594D6FFBE';
        $uuid[]='DF371901-5DF2-40C4-8C6A-2F13954A180A';
        $uuid[]='DF3C24F7-D286-770D-86ED-9D1EFA3D8CF4';
        $uuid[]='E06ACC05-EFEB-41EE-A9AA-9F250C696F8B';
        $uuid[]='E07CEF59-8E57-4464-93D6-C4378B9F6566';
        $uuid[]='E0A495EA-4AD9-402B-90A8-A90523EFC4CB';
        $uuid[]='E152F397-8C17-49BA-93A4-65E5B4FE9C3A';
        $uuid[]='E24EC296-2080-B700-DB67-CFA943550499';
        $uuid[]='E2C1ED20-FD9C-4F8C-91EA-9778ACB1F066';
        $uuid[]='E2F6D6FB-2EB9-4E74-96D7-A6E1107F3E73';
        $uuid[]='E3EA8C61-40E9-495B-8145-F718ECEAB055';
        $uuid[]='E504FBE1-BDF1-4B34-AA9B-17FCAD0CF636';
        $uuid[]='E572D806-36EC-4BF5-A255-D1BAAF95B55B';
        $uuid[]='E64A7025-95B7-4376-9AB0-4C291CA10B5F';
        $uuid[]='E69EC81B-97A5-4D69-9A9C-F9B71C9B858B';
        $uuid[]='E6A3FE6A-7ACC-524C-21C5-2E9E684748C8';
        $uuid[]='E7898A53-54BF-4535-85CD-A25CBDE5A44A';
        $uuid[]='E875E47E-EA93-4F32-BDE6-B71A86C05C80';
        $uuid[]='E91D4D09-0828-42AF-AAA2-2206E565B15D';
        $uuid[]='E9D240B9-744D-441F-98D1-AFC38E464344';
        $uuid[]='EBA99835-0B6D-4906-A7A9-D90102E65DCD';
        $uuid[]='EBB5431A-653B-0952-960B-68A67930028E';
        $uuid[]='EBE2DD90-119B-48E3-06D6-7904F5E9648A';
        $uuid[]='EC18CD30-3D7A-459D-BE1E-BD0756330879';
        $uuid[]='ED785F83-4FD4-4E83-80FD-7E8709187A58';
        $uuid[]='EDAE442F-CBF2-4BF6-B1DC-4BF98AB48697';
        $uuid[]='EE1F7898-4028-4650-B055-086BB07B93D4';
        $uuid[]='EF6A87F0-EBD0-44DD-1E39-55765CC64B18';
        $uuid[]='F012DB00-DB26-4489-8F00-D1953D53FF6A';
        $uuid[]='F06FD457-BF5B-50ED-4B75-4B3AA98C3CFE';
        $uuid[]='F26B3B0F-4816-BFE7-215D-BE1FE4502A9C';
        $uuid[]='F2BD3C1D-9045-4043-9EA8-4C29589619D9';
        $uuid[]='F31B0A3F-3B6F-4537-B891-A9995433596F';
        $uuid[]='F345709B-01F8-4AB7-BC70-813FE0A4E554';
        $uuid[]='F44FE0FF-0E75-41A5-B29A-BC006E0D49F4';
        $uuid[]='F4810805-60A9-4063-B6E0-58CD61A1D0B7';
        $uuid[]='F5B812B0-B645-AFFF-AE2B-07B18F3ACD4C';
        $uuid[]='F6631C7C-DC60-4F0F-B364-F9D277384681';
        $uuid[]='F67EF749-52BD-4375-AC7E-A6EC36ACB94C';
        $uuid[]='F6A13BA2-0F29-4C59-A2F2-86C66B42524D';
        $uuid[]='F79BE4F1-5CEA-0024-7A8C-288DED649EC7';
        $uuid[]='F9075095-397C-403D-91B1-276FE74B4402';
        $uuid[]='F9513A8E-7F70-4A95-BABD-705268A82320';
        $uuid[]='F9853A55-2CF5-7B12-9CA0-F77118CEC713';
        $uuid[]='F987493F-6555-45DC-AC00-BB9EDC93FE92';
        $uuid[]='F9999ADE-D44D-4AB1-9A29-AD6AEDB90A44';
        $uuid[]='F9A8DEAE-DB45-4383-BC8F-8C0414C88E2C';
        $uuid[]='FA6375FB-9CA6-463F-A47A-035A7AA80330';
        $uuid[]='FB005555-0083-4B83-BCB6-470923F44AA6';
        $uuid[]='FB3D0EEA-7596-49CE-8053-ABDB004B1111';
        $uuid[]='FBB23AD2-0280-4204-810E-016A7470083D';
        $uuid[]='FC2BBDDB-ED43-4E71-A3F3-7B64BC087B64';
        $uuid[]='FC9D3837-96C6-0D23-1115-76F3D6CB4A5F';
        $uuid[]='FCA7CD12-3428-0443-A87C-E10FC3C07E6A';
        $uuid[]='FD4C5944-2ADC-49E5-81E6-E25B251B2BE9';
        $uuid[]='FD71CEFE-F9D8-D8EF-4B79-B6EBE17B77CB';
        $uuid[]='FD763667-D4D5-4AAC-B653-E758B9D7E572';
        $uuid[]='FDB3F724-85D6-48D0-8C51-070CA3DE5325';
        $uuid[]='FDC894E1-945F-94CC-50AC-218984F1D2AD';
        $uuid[]='FDEB676D-4F0A-47B8-A643-3E66875C90F0';
        $uuid[]='FDF0402E-DA37-4E1E-A5BA-424F75CA3519';
        $uuid[]='FE2FBA45-BA72-4043-BD3E-44CAB90E2BF5';
        $uuid[]='FE933078-7059-4F87-BDCB-5ABC617E2E17';
        $uuid[]='FF0E85CF-9C10-E4CF-AEF2-1CD03D3FD139';
        $uuid[]='FF7681B7-06A5-48D2-9123-018848E9E359';
        $uuid[]='FFC5ADCF-47A1-53D7-6CE3-F012B812CD90';
        $uuid[]='a204567e-b5cb-4a80-a011-d6354141c899';
        $uuid[]='a471a35a-34ad-48a8-8578-b16944761717';
        $uuid[]='a4ab6c6c-126f-4092-8f9b-23542a7f5a93';
        $uuid[]='a628c92d-64ae-48be-a0bf-34253a7c3142';
        $uuid[]='a6332fbd-e3ac-49ba-b4c3-bb3814e9ead1';
        $uuid[]='a65a5636-a7df-4bb6-9f81-f507a1740ccd';
        $uuid[]='aa01b176-7b20-44e5-b5fe-a38e0dc07662';
        $uuid[]='aa340d24-f8e2-459c-9d43-442d2a3ed35a';
        $uuid[]='aa71bb15-87cc-479e-9d89-e27b0e72be81';
        $uuid[]='ab52376b-d89c-468d-85d6-80b9a8f31437';
        $uuid[]='abbf840d-ff4c-435e-9a2e-4a58c3b20978';
        $uuid[]='acee547b-7433-4bb5-a1a4-bac2127094d4';
        $uuid[]='ae9ed489-e33b-4849-997f-cf25c01b18d7';
        $uuid[]='afc6e9ce-6925-4dcc-8dc9-03b54cbc7417';
        $uuid[]='b356e47c-f2da-4e00-b355-6b2814eca4d6';
        $uuid[]='b5980f91-16da-43f9-bb35-10abe3022f5c';
        $uuid[]='b7ede9f8-625b-4958-8974-8d611bffe5d7';
        $uuid[]='bd0ab8d3-f8ee-4afb-aee3-a19f5e026c98';
        $uuid[]='d0039657-92f0-4266-b4ce-2554fd649893';
        $uuid[]='d183aaca-eab3-406c-9ccb-fc1a878cf394';
        $uuid[]='d23ff362-64eb-41ba-b1d1-aac89ee8707b';
        $uuid[]='d2e22e6b-338a-47dc-8eb1-c99331c7bcf2';
        $uuid[]='d5095add-b88e-4aa6-9908-96714cbac37e';
        $uuid[]='d6ba4408-9a84-4a06-940b-ecd76a01d7aa';
        $uuid[]='d8198297-530a-4afe-b07a-4e2f768fe630';
        $uuid[]='d8fc13ab-b7ea-46e9-9678-ea3a07010935';
        $uuid[]='dc26a419-4023-45f0-b169-48560abf807c';
        $uuid[]='de325b64-02d6-4c7d-80e3-2f679c5b8b7c';
        $uuid[]='de4ea432-d5ce-49ae-a122-a61842e28fd7';
        $uuid[]='e07139a8-4d9c-4a73-8629-70af7628c3e2';
        $uuid[]='e494ad06-6be8-4848-a3c2-9c06c2c36c73';
        $uuid[]='e584fba8-148a-4f71-91de-19115aa284bd';
        $uuid[]='e9f9be49-53b1-464e-8c59-ea0c7e7b38ee';
        $uuid[]='ec684a41-b17e-4a71-a70c-5d8fb8cb47a7';
        $uuid[]='ef1160e7-b828-4668-b1a4-07075a2a5d5e';
        $uuid[]='f0bb0908-e77b-434a-a212-97ea8737a4d4';
        $uuid[]='f0fe9b11-88af-423d-820a-a2a91647eb9b';
        $uuid[]='f14d41c7-415b-49a7-980b-1f6502a3702a';
        $uuid[]='f1b37a7d-afe6-4e7b-ad92-2f63e98aa0f3';
        $uuid[]='f2472681-2791-401d-a30c-c6fbe5236b5c';
        $uuid[]='f4a334f0-5bf2-4789-8b5c-bee78c6eb017';
        $uuid[]='f4d56f89-336b-4463-bd34-8e1a40bd19e8';
        $uuid[]='f5e9b538-597c-447e-a222-f63c7b5ce45b';
        $uuid[]='f8ce9547-e53d-483a-9e8b-467efcde2a1a';
        $uuid[]='f97e84f0-0061-471e-853a-ab9f5b3ddce7';
        $uuid[]='fb821916-b179-4cad-a48e-6b7d6e2f84d4';
        $uuid[]='fba568e5-41bf-4e4e-a38f-e527507794b5';
        $uuid[]='fbedb567-c0fb-451c-bab9-a1d41bde7377';
        $uuid[]='fd10c716-4ed0-4aaf-9db5-9ceb548bb2f5';
        $uuid[]='fd634a10-77ef-44ce-9f44-3ebc321ff011';
        $uuid[]='fef820f9-cc1a-4488-b932-fec69f388b1a';
        $dir_xml = "uploads/contabilidad/cfd/xml/";
        $dir_descarga = "downloads/fiscal/descarga/";
        foreach ($uuid as $uuid_individual){
            copy($dir_xml.$uuid_individual.".xml", $dir_descarga.$uuid_individual.".xml");
        }
    }
}
