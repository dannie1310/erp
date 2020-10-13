<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:41 AM
 */

namespace App\Services\CTPQ;
use App\Imports\PolizaImport;
use App\Models\CTPQ\Poliza;
use App\Models\CTPQ\Empresa;
use App\Models\CTPQ\TipoPoliza;
use App\PDF\CTPQ\PolizaFormatoT1;
use App\PDF\CTPQ\PolizaFormatoT1A;
use App\PDF\CTPQ\PolizaFormatoT1B;
use Illuminate\Support\Facades\DB;
use App\Repositories\CTPQ\PolizaRepository as Repository;
use Maatwebsite\Excel\Facades\Excel;

class PolizaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * PolizaService constructor.
     * @param Poliza $model
     */
    public function __construct(Poliza $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show(array $data, $id)
    {
        $empresa = Empresa::find($data["id_empresa"]);
        DB::purge('cntpq');
        \Config::set('database.connections.cntpq.database',$empresa->AliasBDD);
        return $this->repository->show($id);
    }

    public function store(array $data)
    {
        return $this->repository->create($data);
    }

    public function update(array $data, $id)
    {
        $empresa = Empresa::find($data["id_empresa"]);
        $data["empresa"] = $empresa->AliasBDD;
        DB::purge('cntpq');
        \Config::set('database.connections.cntpq.database',$empresa->AliasBDD);
        $poliza = $this->repository->show($id);
        if($poliza->Ejercicio == 2015){
            abort(500,"No se pueden editar pólizas del año 2015");
        }
        return $this->repository->update($data, $id);
    }

    public function paginate($data)
    {
        try {
            $empresa = Empresa::find($data["id_empresa"]);
            DB::purge('cntpq');
            \Config::set('database.connections.cntpq.database', $empresa->AliasBDD);
            $poliza = $this->repository;

            if (isset($data['ejercicio'])) {
                if ($data['ejercicio'] != "") {
                    $poliza->where([['Ejercicio', '=', $data['ejercicio']]]);
                }
            }

            if (isset($data['periodo'])) {
                if ($data['periodo'] != "") {
                    $poliza->where([['Periodo', '=', $data['periodo']]]);
                }
            }

            if (isset($data['folio'])) {
                if($data['folio'] != '') {
                    $poliza = $poliza->where([['Folio', '=', request('folio')]]);
                }
            }

            if (isset($data['concepto']))
            {
                if ($data['concepto'] != "") {
                    $poliza = $poliza->where([['Concepto','like', '%'.$data['concepto'].'%']]);
                }
            }

            if (isset($data['tipo'])) {
                if($data['tipo'] != '') {
                    $tipo = TipoPoliza::where('Nombre', 'like', '%'.ucfirst(request('tipo')).'%')->first();
                    if($tipo) {
                        $poliza = $poliza->where([['TipoPol', '=', $tipo->Id]]);
                    }else{
                        $poliza = $poliza->where([['TipoPol', '=', 0]]);
                    }
                }
            }
           return $poliza->paginate($data);
        }catch (\Exception $e) {
            abort(500, "No tiene permiso de consultar la base de dato: ".$empresa->AliasBDD.".");
            throw $e;
        }
    }

    public function pdf($data, $id)
    {
        $empresa = Empresa::find($data["id_empresa"]);
        $pdf = new PolizaFormatoT1A($this->show($data->all(), $id), $empresa);
        return $pdf->create();
    }

    public function pdfCaidaB($data, $id)
    {
        $empresa = Empresa::find($data["id_empresa"]);
        $pdf = new PolizaFormatoT1B($this->show($data, $id), $empresa);
        return $pdf->create();
    }

    public function busquedaExcel($data)
    {
        try {
            $empresa = Empresa::find($data["id_empresa"]);
            DB::purge('cntpq');
            \Config::set('database.connections.cntpq.database', $empresa->AliasBDD);
            $file = $this->getFileXLS($data['name'], $data['file']);
            $partidas = $this->getPartidas($file);
            return $this->repository->whereIn(['Ejercicio', $partidas['ejercicios']])->whereIn(['Periodo', $partidas['periodos']])
                ->whereIn(['Folio', $partidas['folios']])->whereIn(['TipoPol', $partidas['tipo']])->all();
        }catch (\Exception $e) {
            abort(500, "No tiene permiso de consultar la base de dato: ".$empresa->AliasBDD.".");
            throw $e;
        }
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

    private function generaDirectorios($nombre_archivo)
    {
        $nombre = str_replace('.xlsx', '-', $nombre_archivo) . date("Ymdhis") . ".xlsx";
        $dir_xls = "uploads/CTPQ/poliza/";
        $path_xls = $dir_xls . $nombre;

        if (!file_exists($dir_xls) && !is_dir($dir_xls)) {
            mkdir($dir_xls, 777, true);
        }
        return ["path_xls" => $path_xls, "dir_xls" => $dir_xls];
    }

    private function getPartidas($file)
    {
        $partidas = Excel::toArray(new PolizaImport, $file);
        $ejercicios = array();
        $periodos = array();
        $tipo = array();
        $folios = array();
        foreach ($partidas[0] as $key => $p) {
            if ($key > 0) {
                if (array_search((int)$p[0], $ejercicios) == false) {
                    $ejercicios[$key] = (int)$p[0];
                }
                if (array_search((int)$p[1], $periodos) == false) {
                    $periodos[$key] = (int)$p[1];
                }
                if (array_search((int)$p[2], $tipo) == false) {
                    $tipo[$key] = (int)$p[2];
                }
                if (array_search((int)$p[3], $folios) == false) {
                    $folios[$key] = (int)$p[3];
                }

            }
        }

        return [
            'ejercicios' => $ejercicios,
            'periodos' => $periodos,
            'tipo' => $tipo,
            'folios' => $folios
        ];
    }
}
