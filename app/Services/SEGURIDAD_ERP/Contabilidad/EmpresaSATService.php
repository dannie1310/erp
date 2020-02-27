<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 26/02/2020
 * Time: 03:30 PM
 */

namespace App\Services\SEGURIDAD_ERP\Contabilidad;


use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\EmpresaSATRepository as Repository;
use Illuminate\Support\Facades\Storage;
use Chumper\Zipper\Zipper;

class EmpresaSATService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(EmpresaSAT $model)
    {
        $this->repository = new Repository($model);
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

    private function generaDirectorios()
    {
        $nombre = date("Ymdhis");
        $nombre_zip = $nombre.".zip";
        $dir_zip = "uploads/contabilidad/cfd/zip/";
        $dir_xml = "uploads/contabilidad/cfd/xml/";
        $path_xml = $dir_xml . $nombre;
        $path_zip = $dir_zip . $nombre_zip;
        if ( !file_exists($dir_zip) && !is_dir($dir_zip) ) {
            mkdir($dir_zip,777,true);
        }
        if ( !file_exists($dir_xml) && !is_dir($dir_xml) ) {
            mkdir($dir_xml,777,true);
        }
        return ["path_zip"=>$path_zip,"path_xml"=>$path_xml,"dir_xml"=>$dir_xml];
    }

    public function procesaZIPCFD($archivo_zip)
    {
        $paths = $this->generaDirectorios();
        $exp = explode("base64,",$archivo_zip);
        //dd(Storage::disk('xml_sat'));
        /*Storage::disk('portal_zip')->delete();
        Storage::disk('portal_zip')->allFiles()*/
        $data = base64_decode($exp[1]);

        $file = public_path($paths["path_zip"]);
        file_put_contents($file, $data);


        $zipper = new Zipper;
        $contenido = $zipper->make(public_path($paths["path_zip"]))->listFiles();
        $zipper->make(public_path($paths["path_zip"]))->extractTo(public_path($paths["path_xml"]));
        $this->procesaCFD($paths["path_xml"]);

    }
    private function procesaCFD($path)
    {
        $dir = opendir($path);
        while ($current = readdir($dir))
        {
            if($current!="." && $current!=".."){
                $this->setArregloFactura($path."/".$current);
                Storage::disk('xml_sat')->put($current,fopen($path."/".$current,"r"));
            }
        }
    }

    private function setArregloFactura($archivo_xml)
    {
        try {
            libxml_use_internal_errors(true);
            $factura_xml = simplexml_load_file($archivo_xml);
            //(string)$factura_xml["version"]
            if((string)$factura_xml["version"] == "3.2")
            {
                $this->arreglo_factura["version"] = (string)$factura_xml["version"];
                $this->setArreglo32($factura_xml);
            } else if($factura_xml["version"] == "3.3")
            {
                $this->arreglo_factura["version"] = (string)$factura_xml["Version"];
                $this->setArreglo33($factura_xml);
            }





        } catch (\Exception $e) {
            abort(500, "Hubo un error al leer el archivo XML proporcionado: " . $e->getMessage());
        }


    }

    private function setArreglo33($factura_xml)
    {
        $this->arreglo_factura["total"] = (float)$factura_xml["Total"];
        $this->arreglo_factura["serie"] = (string)$factura_xml["Serie"];
        $this->arreglo_factura["folio"] = (string)$factura_xml["Folio"];
        $this->arreglo_factura["fecha"] = (string)$factura_xml["Fecha"];
        $this->arreglo_factura["version"] = (string)$factura_xml["Version"];
        $this->arreglo_factura["moneda"] = (string)$factura_xml["Moneda"];
        $emisor = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Emisor')[0];
        $this->arreglo_factura["emisor"]["rfc"] = (string)$emisor["Rfc"][0];
        $this->arreglo_factura["emisor"]["nombre"] = (string)$emisor["Nombre"][0];
        $receptor = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Receptor')[0];
        $this->arreglo_factura["receptor"]["rfc"] = (string)$receptor["Rfc"][0];
        $this->arreglo_factura["receptor"]["nombre"] = (string)$receptor["Nombre"][0];

        try {
            $ns = $factura_xml->getNamespaces(true);
            $factura_xml->registerXPathNamespace('c', $ns['cfdi']);
            $factura_xml->registerXPathNamespace('t', $ns['tfd']);
            $complemento = $factura_xml->xpath('//t:TimbreFiscalDigital')[0];
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
            abort(500, "Hubo un error al leer la ruta de complemento: " . $e->getMessage());
        }

        $this->arreglo_factura["empresa_bd"] = $this->repository->getEmpresa(
            [
                "rfc" => $this->arreglo_factura["emisor"]["rfc"],
                "razon_social" => $this->arreglo_factura["emisor"]["nombre"]
            ]
        );


        dd($factura_xml, $this->arreglo_factura);

    }

    private function setArreglo32($factura_xml)
    {
        $this->arreglo_factura["subtotal"] = (float)$factura_xml["subTotal"];
        $this->arreglo_factura["descuento"] = (float)$factura_xml["descuento"];
        $this->arreglo_factura["total"] = (float)$factura_xml["total"];
        $this->arreglo_factura["total"] = (float)$factura_xml["total"];
        $this->arreglo_factura["serie"] = (string)$factura_xml["serie"];
        $this->arreglo_factura["folio"] = (string)$factura_xml["folio"];
        $this->arreglo_factura["fecha"] = (string)$factura_xml["fecha"];
        $emisor = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Emisor')[0];
        $this->arreglo_factura["emisor"]["rfc"] = (string)$emisor["rfc"][0];
        $this->arreglo_factura["emisor"]["nombre"] = (string)$emisor["nombre"][0];
        $receptor = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Receptor')[0];
        $this->arreglo_factura["receptor"]["rfc"] = (string)$receptor["rfc"][0];
        $this->arreglo_factura["receptor"]["nombre"] = (string)$receptor["nombre"][0];
        $ns = $factura_xml->getNamespaces(true);
        try{
            $impuestos = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Impuestos')[0];
            $this->arreglo_factura["impuestos"]["totalImpuestosTrasladados"] = (string)$impuestos["totalImpuestosTrasladados"][0];
            dd($factura_xml->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Traslados')[0]["importe"]);

        } catch (\Exception $e) {
            abort(500, "Hubo un error al leer la ruta de impuestos: " . $e->getMessage());
        }

        try {

            $factura_xml->registerXPathNamespace('c', $ns['cfdi']);
            $factura_xml->registerXPathNamespace('t', $ns['tfd']);
            $complemento = $factura_xml->xpath('//t:TimbreFiscalDigital')[0];
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
            abort(500, "Hubo un error al leer la ruta de complemento: " . $e->getMessage());
        }
        dd($factura_xml, $this->arreglo_factura);

    }

}