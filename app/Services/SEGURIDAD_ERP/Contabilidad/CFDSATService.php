<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 05:14 PM
 */

namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use DateTime;
use App\Utils\CFD;
use App\Utils\Util;
use App\Utils\Files;
use App\PDF\Fiscal\CFDI;
use Webpatser\Uuid\Uuid;
use App\Events\CambioEFOS;
use Chumper\Zipper\Zipper;
use App\Events\FinalizaCargaCFD;
use App\Events\CambioNoLocalizados;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\PDF\Fiscal\InformeCFDICompleto;
use Illuminate\Support\Facades\Storage;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\CargaCFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT as Model;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\CFDSATRepository as Repository;

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

        if (isset($data['startDate'])) {
            $this->repository->where([['cfd_sat.fecha', '>=', $data['startDate']]]);
        }
        if (isset($data['endDate'])) {
            $this->repository->where([['cfd_sat.fecha', '<=', $data['endDate']]]);
        }
        if (isset($data['rfc_emisor'])) {
            $this->repository->where([['rfc_emisor', 'LIKE', '%' . $data['rfc_emisor'] . '%']]);
        }
        if (isset($data['emisor'])) {
            $proveedoresSAT = ProveedorSAT::query()->where([['razon_social', 'LIKE', '%' . $data['emisor'] . '%']])->get();
            foreach ($proveedoresSAT as $e) {
                $arreglo_proveedor[] = $e->id;
            }
            $this->repository->whereIn(['id_proveedor_sat', $arreglo_proveedor]);
        }
        if (isset($data['rfc_receptor'])) {
            $this->repository->where([['rfc_receptor', 'LIKE', '%' . $data['rfc_receptor'] . '%']]);
        }
        if (isset($data['receptor'])) {
            $empresasSAT = EmpresaSAT::query()->where([['razon_social', 'LIKE', '%' . $data['receptor'] . '%']])->get();
            foreach ($empresasSAT as $es) {
                $arreglo_empresa[] = $es->id;
            }
            $this->repository->whereIn(['id_empresa_sat', $arreglo_empresa]);
        }
        if (isset($data['uuid'])) {
            $this->repository->where([['cfd_sat.uuid', 'LIKE', '%' . $data['uuid'] . '%']]);
        }
        if (isset($data['moneda'])) {
            $this->repository->where([['moneda', 'LIKE', '%' . $data['moneda'] . '%']]);
        }
        if (isset($data['total'])) {
            $this->repository->where([['total', '=', $data['total'] ]]);
        }
        if (isset($data['tipo_cambio'])) {
            $this->repository->where([['tipo_cambio', '=', $data['tipo_cambio'] ]]);
        }
        if (isset($data['subtotal'])) {
            $this->repository->where([['subtotal', '=', $data['subtotal'] ]]);
        }
        if (isset($data['descuento'])) {
            $this->repository->where([['descuento', '=', $data['descuento'] ]]);
        }
        if (isset($data['impuestos_retenidos'])) {
            $this->repository->where([['total_impuestos_retenidos', '=', $data['impuestos_retenidos'] ]]);
        }
        if (isset($data['impuestos_trasladados'])) {
            $this->repository->where([['total_impuestos_trasladados', '=', $data['impuestos_trasladados'] ]]);
        }
        if (isset($data['fecha'])) {
            $this->repository->whereBetween( ['cfd_sat.fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }
        if (isset($data['tipo_comprobante'])) {
            $this->repository->where([['cfd_sat.tipo_comprobante', 'LIKE', '%' .$data['tipo_comprobante']. '%' ]]);
        }
        if (isset($data['serie'])) {
            $this->repository->where([['cfd_sat.serie', 'like', '' .$data['serie']. '' ]]);
        }
        if (isset($data['folio'])) {
            $this->repository->where([['cfd_sat.folio', 'like', '' .$data['folio']. '' ]]);
        }
        if (isset($data['estado'])) {
            if (strpos('CANCELADO', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['cancelado', '=', 1]]);
            }
            else if (strpos('VIGENTE', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['cancelado', '=', 0]]);
            }
        }
        if (isset($data['obra'])) {
            $obras = ConfiguracionObra::withoutGlobalScopes()->where([['nombre', 'LIKE', '%' . $data['obra'] . '%']])->get();

            foreach($obras as $obra){
                $id_obra[] = $obra->id_obra;
                $id_proyecto[] = $obra->id_proyecto;
            }

            $uuid = FacturaRepositorio::whereIn("id_obra", $id_obra)->whereIn("id_proyecto", $id_proyecto)->pluck("uuid");
            $this->repository->whereIn(['cfd_sat.uuid', $uuid]);
        }
        if (isset($data['base_datos'])) {
            $id_proyecto = Proyecto::where([['base_datos', 'LIKE', '%' . $data['base_datos'] . '%']])->pluck("id");

            $uuid = FacturaRepositorio::whereIn("id_proyecto", $id_proyecto)->whereIn("id_proyecto", $id_proyecto)->pluck("uuid");
            $this->repository->whereIn(['cfd_sat.uuid', $uuid]);
        }
        if (isset($data['solo_pendientes'])) {
            if($data['solo_pendientes']==="true"){
                $this->repository->whereDoesntHave("facturaRepositorio")->whereDoesntHave("polizaCFDI");
            }
        }

        if (isset($data['solo_asociados'])) {
            if($data['solo_asociados']==="true"){
                $this->repository->whereHas("facturaRepositorio");
            }
        }

        if (isset($data['solo_asociados_contabilidad'])) {
            if($data['solo_asociados_contabilidad']==="true"){
                $this->repository->whereHas("polizaCFDI");
            }
        }

        if (isset($data['base_datos_ctpq'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_bd", "pol_bd.uuid","=","cfd_sat.uuid")
                ->where([['pol_bd.base_datos_contpaq', 'like', '%' .$data['base_datos_ctpq']. '%' ]])->select("cfd_sat.*");
        }
        if (isset($data['ejercicio'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_eje", "pol_eje.uuid","=","cfd_sat.uuid")
                ->where([['pol_eje.ejercicio', '=', $data['ejercicio'] ]])->select("cfd_sat.*");
        }
        if (isset($data['periodo'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_per", "pol_per.uuid","=","cfd_sat.uuid")
                ->where([['pol_per.periodo', '=', $data['periodo'] ]])->select("cfd_sat.*");
        }
        if (isset($data['tipo_poliza'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_tipo", "pol_tipo.uuid","=","cfd_sat.uuid")
                ->where([['pol_tipo.tipo', 'like', '%' .$data['tipo_poliza']. '%' ]])->select("cfd_sat.*");
        }
        if (isset($data['folio_poliza'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_folio", "pol_folio.uuid","=","cfd_sat.uuid")
                ->where([['pol_folio.folio', 'like', '%' .$data['folio_poliza']. '%' ]])->select("cfd_sat.*");
        }
        if (isset($data['fecha_poliza'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_fecha", "pol_fecha.uuid","=","cfd_sat.uuid")
                ->whereBetween( ['pol_fecha.fecha', [ request( 'fecha_poliza' )." 00:00:00",request( 'fecha_poliza' )." 23:59:59"]] )->select("cfd_sat.*");
        }
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
        $this->repository->actualizaNoLocalizados($this->carga);
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
        //$carga = CargaCFDSAT::find(213);
        //event(new CambioEFOS($carga->cambios));

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
                                            Storage::disk('xml_sat')->put($this->arreglo_factura["uuid"] . ".xml", fopen($ruta_archivo, "r"));
                                            unlink($ruta_archivo);
                                            $this->log["archivos_cargados"] += 1;
                                            $this->log["cfd_cargados"] += 1;
                                        }
                                    } else {
                                        $this->log["cfd_no_cargados"] += 1;
                                        $this->log["archivos_no_cargados"] += 1;
                                        $this->log["archivos_receptor_no_valido"] += 1;
                                        $this->log["receptores_no_validos"][] = $this->arreglo_factura["receptor"];
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
            $this->arreglo_factura["emisor"]["regimen_fiscal"] = (string)Util::eliminaCaracteresEspeciales($emisor["RegimenFiscal"][0]);
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

    public function descargarIndividual($id)
    {
        $uuid =  $this->repository->show($id);

        $dir_xml = "uploads/contabilidad/XML_SAT/";
        $dir_descarga = "downloads/fiscal/descarga/".date("Ymdhis")."/";
        if (!file_exists($dir_descarga) && !is_dir($dir_descarga)) {
            mkdir($dir_descarga, 777, true);
        }
        try{
            copy($dir_xml.$uuid->uuid.".xml", $dir_descarga.$uuid->uuid.".xml");
        }catch (\Exception $e){
        }

        if(file_exists(public_path($dir_descarga.$uuid->uuid.".xml"))){
            return response()->download(public_path($dir_descarga.$uuid->uuid.".xml"));
        } else {
            return response()->json(["mensaje"=>"No hay CFDI para la descarga: ".$uuid->uuid]);
        }
    }

    public function descargar($data){
        if (isset($data['startDate'])) {
            $this->repository->where([['cfd_sat.fecha', '>=', $data['startDate']]]);
        }
        if (isset($data['endDate'])) {
            $this->repository->where([['cfd_sat.fecha', '<=', $data['endDate']]]);
        }
        if (isset($data['rfc_emisor'])) {
            $this->repository->where([['rfc_emisor', 'LIKE', '%' . $data['rfc_emisor'] . '%']]);
        }
        if (isset($data['emisor'])) {
            $proveedoresSAT = ProveedorSAT::query()->where([['razon_social', 'LIKE', '%' . $data['emisor'] . '%']])->get();
            foreach ($proveedoresSAT as $e) {
                $arreglo_proveedor[] = $e->id;
            }
            $this->repository->whereIn(['id_proveedor_sat', $arreglo_proveedor]);
        }
        if (isset($data['rfc_receptor'])) {
            $this->repository->where([['rfc_receptor', 'LIKE', '%' . $data['rfc_receptor'] . '%']]);
        }
        if (isset($data['receptor'])) {
            $empresasSAT = EmpresaSAT::query()->where([['razon_social', 'LIKE', '%' . $data['receptor'] . '%']])->get();
            foreach ($empresasSAT as $es) {
                $arreglo_empresa[] = $es->id;
            }
            $this->repository->whereIn(['id_empresa_sat', $arreglo_empresa]);
        }
        if (isset($data['uuid'])) {
            $this->repository->where([['cfd_sat.uuid', 'LIKE', '%' . $data['uuid'] . '%']]);
        }
        if (isset($data['moneda'])) {
            $this->repository->where([['moneda', 'LIKE', '%' . $data['moneda'] . '%']]);
        }
        if (isset($data['total'])) {
            $this->repository->where([['total', '=', $data['total'] ]]);
        }
        if (isset($data['tipo_cambio'])) {
            $this->repository->where([['tipo_cambio', '=', $data['tipo_cambio'] ]]);
        }
        if (isset($data['subtotal'])) {
            $this->repository->where([['subtotal', '=', $data['subtotal'] ]]);
        }
        if (isset($data['descuento'])) {
            $this->repository->where([['descuento', '=', $data['descuento'] ]]);
        }
        if (isset($data['impuestos_retenidos'])) {
            $this->repository->where([['total_impuestos_retenidos', '=', $data['impuestos_retenidos'] ]]);
        }
        if (isset($data['impuestos_trasladados'])) {
            $this->repository->where([['total_impuestos_trasladados', '=', $data['impuestos_trasladados'] ]]);
        }
        if (isset($data['fecha'])) {
            $this->repository->whereBetween( ['cfd_sat.fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }
        if (isset($data['tipo_comprobante'])) {
            $this->repository->where([['cfd_sat.tipo_comprobante', 'LIKE', '%' .$data['tipo_comprobante']. '%' ]]);
        }
        if (isset($data['serie'])) {
            $this->repository->where([['cfd_sat.serie', 'like', '' .$data['serie']. '' ]]);
        }
        if (isset($data['folio'])) {
            $this->repository->where([['cfd_sat.folio', 'like', '' .$data['folio']. '' ]]);
        }
        if (isset($data['estado'])) {
            if (strpos('CANCELADO', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['cancelado', '=', 1]]);
            }
            else if (strpos('VIGENTE', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['cancelado', '=', 0]]);
            }
        }
        if (isset($data['obra'])) {
            $obras = ConfiguracionObra::withoutGlobalScopes()->where([['nombre', 'LIKE', '%' . $data['obra'] . '%']])->get();

            foreach($obras as $obra){
                $id_obra[] = $obra->id_obra;
                $id_proyecto[] = $obra->id_proyecto;
            }

            $uuid = FacturaRepositorio::whereIn("id_obra", $id_obra)->whereIn("id_proyecto", $id_proyecto)->pluck("uuid");
            $this->repository->whereIn(['cfd_sat.uuid', $uuid]);
        }
        if (isset($data['base_datos'])) {
            $id_proyecto = Proyecto::where([['base_datos', 'LIKE', '%' . $data['base_datos'] . '%']])->pluck("id");

            $uuid = FacturaRepositorio::whereIn("id_proyecto", $id_proyecto)->whereIn("id_proyecto", $id_proyecto)->pluck("uuid");
            $this->repository->whereIn(['cfd_sat.uuid', $uuid]);
        }

        if (isset($data['solo_pendientes'])) {
            if($data['solo_pendientes']==="true"){
                $this->repository->whereDoesntHave("facturaRepositorio")->whereDoesntHave("polizaCFDI");
            }
        }

        if (isset($data['solo_asociados'])) {
            if($data['solo_asociados']==="true"){
                $this->repository->whereHas("facturaRepositorio");
            }
        }

        if (isset($data['solo_asociados_contabilidad'])) {
            if($data['solo_asociados_contabilidad']==="true"){
                $this->repository->whereHas("polizaCFDI");
            }
        }

        if (isset($data['base_datos_ctpq'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_bd", "pol_bd.uuid","=","cfd_sat.uuid")
                ->where([['pol_bd.base_datos_contpaq', 'like', '%' .$data['base_datos_ctpq']. '%' ]])->select("cfd_sat.*");
        }
        if (isset($data['ejercicio'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_eje", "pol_eje.uuid","=","cfd_sat.uuid")
                ->where([['pol_eje.ejercicio', '=', $data['ejercicio'] ]])->select("cfd_sat.*");
        }
        if (isset($data['periodo'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_per", "pol_per.uuid","=","cfd_sat.uuid")
                ->where([['pol_per.periodo', '=', $data['periodo'] ]])->select("cfd_sat.*");
        }
        if (isset($data['tipo_poliza'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_tipo", "pol_tipo.uuid","=","cfd_sat.uuid")
                ->where([['pol_tipo.tipo', 'like', '%' .$data['tipo_poliza']. '%' ]])->select("cfd_sat.*");
        }
        if (isset($data['folio_poliza'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_folio", "pol_folio.uuid","=","cfd_sat.uuid")
                ->where([['pol_folio.folio', 'like', '%' .$data['folio_poliza']. '%' ]])->select("cfd_sat.*");
        }
        if (isset($data['fecha_poliza'])) {
            $this->repository->join("Contabilidad.polizas_cfdi as pol_fecha", "pol_fecha.uuid","=","cfd_sat.uuid")
                ->whereBetween( ['pol_fecha.fecha', [ request( 'fecha_poliza' )." 00:00:00",request( 'fecha_poliza' )." 23:59:59"]] )->select("cfd_sat.*");
        }

        $uuid =  $this->repository->all();

        $dir_xml = "uploads/contabilidad/XML_SAT/";
        $dir_descarga = "downloads/fiscal/descarga/".date("Ymdhis")."/";
        if (!file_exists($dir_descarga) && !is_dir($dir_descarga)) {
            mkdir($dir_descarga, 777, true);
        }
        foreach ($uuid as $uuid_individual){
            try{
                copy($dir_xml.$uuid_individual->uuid.".xml", $dir_descarga.$uuid_individual->uuid.".xml");
            }catch (\Exception $e){

            }

        }

        $path = "downloads/fiscal/descarga/";
        $nombre_zip = $path.date("Ymd_his").".zip";

        $zipper = new Zipper;
        $zipper->make(public_path($nombre_zip))
            ->add(public_path($dir_descarga));
        $zipper->close();

        Files::eliminaDirectorio($dir_descarga);

        if(file_exists(public_path($nombre_zip))){
            return response()->download(public_path($nombre_zip));
        } else {
            return response()->json(["mensaje"=>"No hay CFDI para la descarga "]);
        }
    }

    public function pdfCFDI($id)
    {
        $CFDI = $this->repository->show($id);
        try{
            $cfd = new CFD($CFDI->xml);
        } catch (\Exception $e){
            dd("No se cargo el CFDI");
        }

        $arreglo_cfd = $cfd->getArregloFactura();
        //dd($arreglo_cfd);
        $pdf = new CFDI($arreglo_cfd);
        return $pdf;
    }

    public function cargaXMLProveedor(array $data)
    {
        $archivo_xml = $data["xml"];
        $cfd = new CFD($archivo_xml);
        $arreglo_cfd = $cfd->getArregloFactura();
        if(auth()->user()->usuario != $arreglo_cfd["emisor"]["rfc"]){
            abort(500, "El emisor de los CFDI no coincide con su usuario, favor de verificar");
        }
        $this->validaReceptor($arreglo_cfd);

        $arreglo_cfd["id_empresa_sat"] = $this->repository->getIdEmpresa($arreglo_cfd["receptor"]);
        $proveedor = $this->repository->getProveedorSAT($arreglo_cfd["emisor"], $arreglo_cfd["id_empresa_sat"]);
        $arreglo_cfd["id_proveedor_sat"] = $proveedor["id_proveedor"];

        $exp = explode("base64,", $data["xml"]);
        $contenido_xml = base64_decode($exp[1]);
        $arreglo_cfd["contenido_xml"] = $contenido_xml;

        $this->validaTipoTransaccion($data["id_tipo_transaccion"], $arreglo_cfd["tipo_comprobante"]);

        $arreglo_cfd["id_tipo_transaccion"] = $data["id_tipo_transaccion"];
        $cfd->validaCFDI33($contenido_xml);

        $cfdi = $this->registraCFDI($arreglo_cfd);




        return $cfdi;
    }

    private function validaDisponibilidad($cfdi)
    {
        if($cfdi->id_solicitud_recepcion>0){
            if($cfdi->solicitudRecepcion->estado>=0){
                abort(500, "Este CFDI esta asociado a la solicitud de revisión con número de folio: ". $cfdi->solicitudRecepcion->numero_folio);
            }
        }
    }

    private function validaTipoTransaccion($id_tipo_transaccion, $tipo_comprobante_actual)
    {
        $tipo_transaccion = $this->repository->getTipoTransaccion($id_tipo_transaccion);
        $tipo_comprobante = $tipo_transaccion->tipo_comprobante;
        $tipoComprobanteActual = $this->repository->getTipoComprobante($tipo_comprobante_actual);
        if($tipo_comprobante != $tipo_comprobante_actual){
            abort(500, "El tipo de transacción seleccionada: '".$tipo_transaccion->descripcion."' requiere un CFDI tipo: '".$tipo_transaccion->tipoComprobante->descripcion."', el CFDI seleccionado es de tipo: '".$tipoComprobanteActual->descripcion."'");
        }
    }

    private function validaReceptor($arreglo_cfd)
    {
        $rfc_receptoras = $this->repository->getRFCReceptoras();
        if (!in_array( $arreglo_cfd["receptor"]["rfc"], $rfc_receptoras)) {
            abort(500, "El RFC del receptor en el comprobante digital (" . $arreglo_cfd["receptor"]["rfc"] . ") no esta dado de alta en los registros de Hermes Infraestructura.");
        }
    }

    private function registraCFDI($arreglo_factura)
    {
        $contenido_archivo_xml = $arreglo_factura["contenido_xml"];
        if (key_exists("uuid", $arreglo_factura)) {
            $cfdi = $this->repository->validaExistencia($arreglo_factura["uuid"]);
            if (!$cfdi) {
                $arreglo_factura["xml_file"] = $this->repository->getArchivoSQL(base64_encode($contenido_archivo_xml));
                $cfdi = $this->store($arreglo_factura);
                if ($cfdi) {
                    Storage::disk('xml_sat')->put($arreglo_factura["uuid"].".xml", $contenido_archivo_xml);
                }
            } else {
                $this->validaDisponibilidad($cfdi);
                if($cfdi->id_tipo_transaccion != $arreglo_factura["id_tipo_transaccion"])
                {
                    $cfdi->id_tipo_transaccion = $arreglo_factura["id_tipo_transaccion"];
                    $cfdi->save();
                    $cfdi->eliminaDocumentos();
                    $cfdi->actualizaObligatoriedadDocumentos();
                }
            }

            $cfdi->generaDocumentos();
            $cfdi->load("archivos");

            return $cfdi;
        }
        return null;
    }

    public function procesar($data){
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <cfdi:Comprobante Version="3.3" xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd" Folio="190" Fecha="2021-05-28T11:12:56" NoCertificado="00001000000407130925" Certificado="MIIGRDCCBCygAwIBAgIUMDAwMDEwMDAwMDA0MDcxMzA5MjUwDQYJKoZIhvcNAQELBQAwggGyMTgwNgYDVQQDDC9BLkMuIGRlbCBTZXJ2aWNpbyBkZSBBZG1pbmlzdHJhY2nDs24gVHJpYnV0YXJpYTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMR8wHQYJKoZIhvcNAQkBFhBhY29kc0BzYXQuZ29iLm14MSYwJAYDVQQJDB1Bdi4gSGlkYWxnbyA3NywgQ29sLiBHdWVycmVybzEOMAwGA1UEEQwFMDYzMDAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBEaXN0cml0byBGZWRlcmFsMRQwEgYDVQQHDAtDdWF1aHTDqW1vYzEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMV0wWwYJKoZIhvcNAQkCDE5SZXNwb25zYWJsZTogQWRtaW5pc3RyYWNpw7NuIENlbnRyYWwgZGUgU2VydmljaW9zIFRyaWJ1dGFyaW9zIGFsIENvbnRyaWJ1eWVudGUwHhcNMTcwODAzMTc1NDMxWhcNMjEwODAzMTc1NDMxWjCB5DEtMCsGA1UEAxMkVkFMVlVMQVMgWSBDT01QVUVSVEFTIFVSQUdBIFNBIERFIENWMS0wKwYDVQQpEyRWQUxWVUxBUyBZIENPTVBVRVJUQVMgVVJBR0EgU0EgREUgQ1YxLTArBgNVBAoTJFZBTFZVTEFTIFkgQ09NUFVFUlRBUyBVUkFHQSBTQSBERSBDVjElMCMGA1UELRMcVkNVMDUwODAzMVQ0IC8gVUFQQTY5MTAwMTZKOTEeMBwGA1UEBRMVIC8gVUFQQTY5MTAwMUhERlJYTjAyMQ4wDAYDVQQLEwVVTklDQTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAIDi4CWvg5rdrskR+vD+d6pcRqDGaBto9Qnh3geAXx1G7MdMmA36WazLhQVWBTcdCKx6Goma4daC8pb6B3AKH8BrYy3ieZLfn34CSqujMyEdAt0Z9n2QGmSVgaZ9sUEfHBjA55i+my1+Tb389d/+8ht39sSi13vEUzezbq4ZLcC2MhdjHHytYzhdc3FHG4gTK0R5lOmnhuYVnlRKgE+6OOQDCX+TeOPKuHkEfyNU6ENNyFLBMK2wOD9Au4mCzaQTEBEzHi3i16ME7d+pCHzBMIMw3Bk7BdZA7lFqkolsJHoWEoCG7QsznkDkg6SMw8PwIRW07Q3b24qhrOa4lR3i8ccCAwEAAaMdMBswDAYDVR0TAQH/BAIwADALBgNVHQ8EBAMCBsAwDQYJKoZIhvcNAQELBQADggIBAKTK/jemyx+Am4+4mTFoA5TANYbkeLELqbvIMczPbEjNxwnxdaeyXA9d2WhfSfQWew0ru+T9+nBU4e/mxoob0yBcIQKw4KzXNTE68HwYyqa+7tqgcKMAZ0K4uShPoWnK/Gz1UOc1JvmvamomDJ31wQj7S3Xe59jVqNKrXl4Is1bLNGPK0kRmO9fwPdNVyqcnvZRFwXZ5etQS9eeohVMXCOEl4amxAaAzmVEdEvgon1gVJ7TtM7G4brMszHvZc3L9TGxIXes82xHlsPh8H7QhdDMf8d7bq/VOT8F0rtm8ewIxTJXkSf9Fu6p572MaMVqGvQyH4pYNCDA0UVXt72kUUrA9kAyFbZHAFNknl7fE2nA5xj0nt/xuQuUbHKt6GL0+J72sdIQG2V3aceqYzT4yFz+0sUqez1DoIVYwVeyFF2OSzBEMLTDqhxu9erzvN6cwUGtHIPS91SmzY7KeRJbYqjIAo0J1Bs+DnnAxn61jrBqwug1bcdQoZux7Lwy6/fYDuKA5Np/GfJ/Ru/fbHY4crpUSDCcOyT6C7/x2dQe1Z6o1n4w4Z7PJc2LOSjVYktnXQqB/banS3bI8lGcXqvwp+0Gb3BbYiwW/bL9Q2r9uChQ5RvyJD4tZeM3iq+fbATz0oL9L3dAPMZC+8MGD5ny2qaSdHikPNYvubLIjFcDNmwNv" Moneda="MXN" TipoDeComprobante="E" MetodoPago="PPD" FormaPago="99" SubTotal="59240.40" Total="68718.86" LugarExpedicion="56610" Sello="IW6rb53+Uks9EoryDn2ARPa7e1lwEccT1eG0cS0yHLBFIM2Yx5/52XZa5Q0Fiu761kQ+6QFQmyTMLZm80Y1bMVQfK4kv1ik86EiRZ8czmqKZNdfZ5GBvq1RNBhCtBnM9nLeze81ElCDpLUhBqJfACYqb6tfdkHBvb5GAwRFNHCKMlmPxLIEkar844Hx/BV62uwuFsYPr8Ydb9XO3oqg4w9iwZnYi+TTqYsCc+sDH7SiGdXBmabN1rQLJw26jgu2TETSpSFhWiSBSDppr96Q5KZb7N6Qw7mto5/aKjQ1golK1vrkfdyYRI8i8ji0FMv1hC31yzmiBlor/zbz9eVpgEA==">
            <cfdi:CfdiRelacionados TipoRelacion="01">
                <cfdi:CfdiRelacionado UUID="647DBEE9-BFCF-11EB-8FBC-00155D014009"/>
            </cfdi:CfdiRelacionados>
            <cfdi:Emisor Rfc="VCU0508031T4" Nombre="VALVULAS Y COMPUERTAS URAGA SA DE CV" RegimenFiscal="601"/>
            <cfdi:Receptor Rfc="PCO811231EI4" Nombre="LA PENINSULAR COMPAÑIA CONSTRUCTORA S.A DE C.V" UsoCFDI="G03"/>
            <cfdi:Conceptos>
                <cfdi:Concepto ClaveProdServ="84111506" Cantidad="1.00" ClaveUnidad="ACT" Descripcion="ANTICIPO DEL 30% SOBRE EL MONTO TOTAL, POR EL SUMINISTRO DE 7 BOTONERAS MARCA URAGA, PARA CONTROLAR ACTUADORES ELECTRICOS MARCA AUMA, EN LA PLANTA DE BOMBEO CILA, DE ACUERDO A LA ORDEN DE COMPRA No. 00055" ValorUnitario="59240.40" Importe="59240.40">
                    <cfdi:Impuestos>
                        <cfdi:Traslados>
                            <cfdi:Traslado Base="59240.40" Impuesto="002" TipoFactor="Tasa" TasaOCuota="0.160000" Importe="9478.46"/>
                        </cfdi:Traslados>
                    </cfdi:Impuestos>
                </cfdi:Concepto>
            </cfdi:Conceptos>
            <cfdi:Impuestos TotalImpuestosTrasladados="9478.46">
                <cfdi:Traslados>
                    <cfdi:Traslado Impuesto="002" TipoFactor="Tasa" TasaOCuota="0.160000" Importe="9478.46"/>
                </cfdi:Traslados>
            </cfdi:Impuestos>
            <cfdi:Complemento>
                <tfd:TimbreFiscalDigital xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/sitio_internet/cfd/TimbreFiscalDigital/TimbreFiscalDigitalv11.xsd" Version="1.1" UUID="906EAD88-BFCF-11EB-91AE-00155D012007" FechaTimbrado="2021-05-28T11:12:56" RfcProvCertif="TBN040609RKA" SelloCFD="IW6rb53+Uks9EoryDn2ARPa7e1lwEccT1eG0cS0yHLBFIM2Yx5/52XZa5Q0Fiu761kQ+6QFQmyTMLZm80Y1bMVQfK4kv1ik86EiRZ8czmqKZNdfZ5GBvq1RNBhCtBnM9nLeze81ElCDpLUhBqJfACYqb6tfdkHBvb5GAwRFNHCKMlmPxLIEkar844Hx/BV62uwuFsYPr8Ydb9XO3oqg4w9iwZnYi+TTqYsCc+sDH7SiGdXBmabN1rQLJw26jgu2TETSpSFhWiSBSDppr96Q5KZb7N6Qw7mto5/aKjQ1golK1vrkfdyYRI8i8ji0FMv1hC31yzmiBlor/zbz9eVpgEA==" NoCertificadoSAT="00001000000504587508" SelloSAT="AZKqoAJSEMKfeCDePA+ZRSgkfStitnQqyZd1fUUEHYnXQGx8/YWXxVP00pkEHYzZoLBGCntcyaSiY1LwuJPUu6PBozaQOr2ubgCxkA6sGDmTauEgDbyalTWwxUvVsyRu9YaxUR5f3nq8vOh9M9Gwp1Nl5VOthsoHc1QaGsh/TJ/N3QxrDrDnEacDylYOgtsyb+bdD6GqknXYE0VHIJfujo36+5KgzHNj1om0B6ShwqcR5oR8BSk5t/Q/2XDKVYmsQv9uqqyIUfJAuTf1wyLwn5vHxpZR4nHTQUAd/z/HAh5sQKTiDLWYzaU5MRjakMvKFc0B+eBy12eJxOYX9ybS/g=="/>
            </cfdi:Complemento>
        </cfdi:Comprobante>';

        // dd($this->get_string_between($xml, '<cfdi:Conceptos>', '</cfdi:Conceptos>'), Uuid::generate()->string);

        $carga_xml = new CFD($xml);
        $array_xml = $carga_xml->getArregloFactura();
        dd($array_xml['certificado']);

        
    }

    function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}
