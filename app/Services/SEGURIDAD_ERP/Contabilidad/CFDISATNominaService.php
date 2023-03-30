<?php

namespace App\Services\SEGURIDAD_ERP\Contabilidad;


use App\Jobs\ProcessCancelacionCFDI;

use App\Models\SEGURIDAD_ERP\Contabilidad\CFDISATNomina;

use App\Repositories\SEGURIDAD_ERP\Contabilidad\CFDISATNominaRepository;
use App\Utils\CFDINomina;
use DateTime;
use DateTimeZone;
use App\Utils\Files;
use App\Events\CambioEFOS;
use Chumper\Zipper\Zipper;
use App\Events\FinalizaCargaCFD;
use App\Events\CambioNoLocalizados;
use Illuminate\Support\Facades\Storage;

class CFDISATNominaService
{
    /**
     * @var CFDISATNominaRepository
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
        $util_cfdi = new CFDINomina($ruta_archivo);
        $arreglo_factura = $util_cfdi->getArreglo();
        $arreglo_factura = $this->complementaArreglo($arreglo_factura);

        if (key_exists("uuid", $arreglo_factura)) {
            if (!$this->repository->validaExistencia($arreglo_factura["uuid"])) {
                if ($arreglo_factura["id_emisor"] > 0) {
                    $arreglo_factura["xml_file"] = $this->repository->getArchivoSQL(base64_encode($contenido_archivo_xml));
                    if ($this->repository->registrar($arreglo_factura)) {
                        unlink($ruta_archivo);
                        $this->log["archivos_cargados"] += 1;
                        $this->log["cfd_cargados"] += 1;
                    }
                } else {
                    $this->log["cfd_no_cargados"] += 1;
                    $this->log["archivos_no_cargados"] += 1;
                    $this->log["archivos_receptor_no_valido"] += 1;
                    $this->log["receptores_no_validos"][] = $arreglo_factura["id_emisor"];
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

    private function complementaArreglo($arreglo_factura)
    {
        $arreglo_factura["id_carga_zip_cfdi"] = $this->carga->id;
        $arreglo_factura["id_emisor"] = $this->repository->getIdEmisor($arreglo_factura["emisor"]);
        if($arreglo_factura["id_emisor"] >0 )
        {
            $arreglo_factura["id_receptor"] = $this->repository->getIdReceptor($arreglo_factura["receptor"]);
        }
        return $arreglo_factura;
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
