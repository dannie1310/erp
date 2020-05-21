<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 05:14 PM
 */

namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Exports\SolicitudEdicionExport;
use App\Http\Transformers\CTPQ\PolizaMovimientoTransformer;
use App\Http\Transformers\CTPQ\PolizaTransformer;
use App\Imports\SolicitudEdicionImport;
use App\Models\CTPQ\Poliza;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicion as Model;
use App\PDF\CTPQ\PolizaFormato;
use App\Repositories\CTPQ\PolizaRepository;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\SolicitudEdicionRepository as Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Chumper\Zipper\Zipper;
use DateTime;
use Maatwebsite\Excel\Facades\Excel;

class SolicitudEdicionService
{
    /**
     * @var Repository
     */
    protected $repository;
    protected $arreglo_solicitud;
    protected $log;
    protected $carga;
    protected $resumen;

    public function __construct(Model $model)
    {
        $this->resumen["cantidad_polizas_involucradas"] = 0;
        $this->resumen["cantidad_partidas"] = 0;
        $this->resumen["cantidad_movimientos"] = 0;
        $this->resumen["cantidad_bases"] = 0;
        $this->resumen["bases"] = [];
        $this->bases = [];
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

    public function store(array $data)
    {
        $solicitud = $this->repository->registrar($data);
        return $solicitud;
    }

    private function generaDirectorios($nombre_archivo)
    {
        $nombre = $nombre_archivo . "_" . date("Ymdhis") . ".xlsx";
        $dir_xls = "uploads/contabilidad/solicitud_edicion/";
        $path_xls = $dir_xls . $nombre;

        if (!file_exists($dir_xls) && !is_dir($dir_xls)) {
            mkdir($dir_xls, 777, true);
        }
        return ["path_xls" => $path_xls, "dir_xls" => $dir_xls];
    }

    private function getDatosPartidas($file_xls)
    {
        $rows = Excel::toArray(new SolicitudEdicionImport, $file_xls);
        $partidas_solicitud = [];
        $i = 0;
        foreach ($rows[0] as $key => $row) {
            if ($key > 0) {
                $fecha = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0]);
                $fecha_obj = DateTime::createFromFormat("Y/m/d", $fecha->format("Y/m/d"));
                $rows[0][$key][0] = $fecha_obj->format("Y/m/d");
                $partidas_solicitud[$i]["fecha"] = $fecha_obj->format("Y/m/d");
                $partidas_solicitud[$i]["fecha_format"] = $fecha_obj->format("d/m/Y");
                $partidas_solicitud[$i]["tipo"] = (int)$row[1];

                $partidas_solicitud[$i]["folio"] = (int)$row[2];
                /*$partidas_solicitud[$i]["importe"] = (float)$row[3];
                $partidas_solicitud[$i]["importe_format"] = "$ " . number_format($row[3], "2", ".", ",");*/
                $partidas_solicitud[$i]["concepto"] = (string)$row[3];
                $partidas_solicitud[$i]["referencia"] = (string)$row[4];
                switch ($row[1]) {
                    case 1:
                        $partidas_solicitud[$i]["tipo_txt"] = "Ingresos";
                        break;
                    case 2:
                        $partidas_solicitud[$i]["tipo_txt"] = "Egresos";
                        break;
                    case 3:
                        $partidas_solicitud[$i]["tipo_txt"] = "Diario";
                        break;
                }
                $i++;
            }
        }
        return $partidas_solicitud;
    }

    private function getFileXLS($nombre_archivo, $archivo_xls)
    {
        $paths = $this->generaDirectorios($nombre_archivo);
        $exp = explode("base64,", $archivo_xls);
        $data = base64_decode($exp[1]);
        $file_xls = public_path($paths["path_xls"]);
        file_put_contents($file_xls, $data);
        return $file_xls;
    }

    private function getDatosPolizas($partidas)
    {
        $poliza_transformer = new PolizaTransformer();
        $movimiento_transformer = new PolizaMovimientoTransformer();
        $repositorio_poliza = new PolizaRepository(new Poliza());
        $lista = $this->repository->getListaBDContpaq();
        $i_partida = 0;
        foreach ($partidas as $partida) {
            $contador_movimientos = 0;
            $contador_bases = 0;
            $polizas = [];
            $i = 0;
            foreach ($lista as $empresa) {
                $polizas_encontradas = [];
                try {
                    DB::purge('cntpq');
                    \Config::set('database.connections.cntpq.database', $empresa->AliasBDD);
                    $polizas_encontradas = $repositorio_poliza->find($partida);
                    if (count($polizas_encontradas) > 0) {
                        $this->bases[] = $empresa->AliasBDD;
                        $contador_bases++;
                    }
                } catch (\Exception $e) {
                    abort(500, "No tiene acceso a la BD: " . $empresa->AliasBDD . " favor de ponerse en contacto con el área de soporte a aplicaciones.".$e->getMessage());
                }

                foreach ($polizas_encontradas as $poliza_encontrada) {
                    $movimientos = [];
                    $im = 0;
                    foreach ($poliza_encontrada->movimientos as $movimiento) {
                        $movimiento_transform = $movimiento_transformer->transform($movimiento);
                        $movimientos[$im] = $movimiento_transform;
                        $movimientos[$im]["id_movimiento"] = $movimiento_transform["id"];
                        $movimientos[$im]["concepto_original"] = $movimiento_transform["concepto"];
                        $movimientos[$im]["referencia_original"] = $movimiento_transform["referencia"];
                        $this->resumen["cantidad_movimientos"]++;
                        $im++;
                    }
                    $poliza_encontrada_transform = $poliza_transformer->transform($poliza_encontrada);
                    $polizas[$i] = $poliza_encontrada_transform;
                    $polizas[$i]["bd_contpaq"] = $empresa->AliasBDD;
                    $polizas[$i]["movimientos"] = $movimientos;
                    $polizas[$i]["concepto_original"] = $poliza_encontrada_transform["concepto"];
                    $polizas[$i]["id_poliza"] = $poliza_encontrada_transform["id"];
                    $polizas[$i]["id_empresa_contpaq"] = $empresa->IdContpaq;
                    $contador_movimientos += count($movimientos);
                    $i++;
                    $this->resumen["cantidad_polizas_involucradas"]++;
                }
            }
            $partidas[$i_partida]["cantidad_movimientos"] = $contador_movimientos;
            $partidas[$i_partida]["cantidad_bases"] = $contador_bases;
            $partidas[$i_partida]["polizas"] = $polizas;
            $i_partida++;
        }
        return $partidas;
    }

    public function impresionPolizas($id){
        $folios  = $this->repository->show($id)->polizas;

  //      $f = $folios->pluck('folio')->toArray();
     //   sort($f);
        foreach ($folios as $folio) {
            DB::purge('cntpq');
            \Config::set('database.connections.cntpq.database', $folio->bd_contpaq);
            $pdf = new PolizaFormato(Poliza::find('338943'),$folio->empresa);
            return $pdf->create();
        }
      //  dd('panda', $folios, $f);
    }

    public function procesaSolicitudXLS($nombre_archivo, $archivo_xls)
    {
        $file_xls = $this->getFileXLS($nombre_archivo, $archivo_xls);
        $partidas = $this->getDatosPartidas($file_xls);
        $this->resumen["cantidad_partidas"] = count($partidas);


        $partidas_con_polizas = $this->getDatosPolizas($partidas);
        $this->resumen["cantidad_bases"] = count(array_unique($this->bases));
        $this->resumen["bases"] = array_unique($this->bases);
        return ["partidas" => $partidas_con_polizas, "resumen" => $this->resumen];
    }

    public function autorizar($id, $datos)
    {
        $polizas = [];
        $contador_aprobadas = 0;
        foreach ($datos as $partida){
            foreach($partida["polizas"]["data"] as $poliza )
            {
                $polizas[] = ["id"=>$poliza["id"], "estado"=>$poliza["estado"]] ;
                if($poliza["estado"])
                {
                    $contador_aprobadas++;
                }
            }
        }
        if(!$contador_aprobadas > 0)
        {
            abort(500,"No puede autorizar una solicitud sin aprobar el cambio de al menos una póliza");
        }
        return $this->repository->autorizar($polizas, $id);
    }

    public function rechazar($id)
    {
        return $this->repository->rechazar($id);
    }

    public function aplicar($id)
    {
        return $this->repository->aplicar($id);
    }

    public function descargarXLS($id)
    {
        $folio = $this->show($id)->numero_folio;
        $solicitud_polizas = array(
            array('a','b'),
            array('c','d'),
        );

        $polizas_solicitud = $this->repository->getPolizasSolicitud($id);

        $export = new SolicitudEdicionExport($polizas_solicitud);

        return Excel::download($export, "SolEdPol_#".$folio."_".date('dmY_His').".xlsx");
    }
}
