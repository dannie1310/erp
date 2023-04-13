<?php

namespace App\Services\SEGURIDAD_ERP\Fiscal;


use App\Jobs\ProcessCancelacionCFDI;

use App\Models\SEGURIDAD_ERP\Contabilidad\CFDISATNomina;

use App\Models\SEGURIDAD_ERP\Fiscal\ConceptosCFDIEmitidos;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\CFDISATNominaRepository;
use App\Repositories\SEGURIDAD_ERP\Fiscal\ConceptosCFDIEmitidosRepository;
use App\Services\SEGURIDAD_ERP\Contabilidad\Model;
use App\Services\SEGURIDAD_ERP\Contabilidad\Repository;
use App\Utils\CFD;
use App\Utils\CFDINomina;
use DateTime;
use DateTimeZone;
use App\Utils\Files;
use App\Events\CambioEFOS;
use Chumper\Zipper\Zipper;
use App\Events\FinalizaCargaCFD;
use App\Events\CambioNoLocalizados;
use Illuminate\Support\Facades\Storage;

class CFDISATEmitidosService
{
    /**
     * @var CFDISATNominaRepository
     */
    protected $repository;
    protected $log;
    protected $carga;

    public function __construct(ConceptosCFDIEmitidos $model)
    {
        $this->repository = new ConceptosCFDIEmitidosRepository($model);

    }



    public function procesaDirectorioZIPCFDI()
    {

        ini_set('max_execution_time', '7200');

        $path = "uploads/contabilidad/zip_cfdi_nomina/";
        $this->preparaDirectorio($path);
        $this->procesaDirectorio($path);



        return null;
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
                    $ruta_archivo = $path . "/" . $current;
                    if (strpos($current, ".xml")) {
                        $this->procesaArchivoCFDI($ruta_archivo, $current);
                    }
                    else {
                        Storage::disk('xml_errores')->put('conceptos_emitidos' . '/tipo_incorrecto/' . $current, fopen($ruta_archivo, "r"));
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
        $util_cfdi = new CFD($ruta_archivo);
        $arreglo_factura = $util_cfdi->getArregloFactura();

        if (key_exists("uuid", $arreglo_factura)) {
            if (!$this->repository->validaExistencia($arreglo_factura["uuid"])) {
                if ($this->repository->registrar($arreglo_factura)) {
                    unlink($ruta_archivo);
                }
            } else {
                unlink($ruta_archivo);
            }
        } else {
            Storage::disk('xml_errores')->put('conceptos_emitidos' . '/corruptos/' . $current, fopen($ruta_archivo, "r"));
            unlink($ruta_archivo);
        }
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
