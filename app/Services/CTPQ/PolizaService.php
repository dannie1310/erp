<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:41 AM
 */

namespace App\Services\CTPQ;
use Chumper\Zipper\Zipper;
use App\Models\CTPQ\Poliza;
use App\Models\CTPQ\Empresa;
use App\Models\CTPQ\TipoPoliza;
use App\PDF\CTPQ\PolizaFormatoT1;

use App\PDF\CTPQ\PolizaFormatoT1A;
use App\PDF\CTPQ\PolizaFormatoT1B;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Repositories\CTPQ\PolizaRepository as Repository;

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

    public function descargaZip($data){
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;

        $empresa = Empresa::find($data["id_empresa"]);
        DB::purge('cntpq');
        \Config::set('database.connections.cntpq.database', $empresa->AliasBDD);
        foreach($this->busqueda($data)->all() as $i => $poliza){
            if($data["caida"] == 1){
                $pdf = new PolizaFormatoT1A($poliza, $empresa);
            }
            if($data["caida"] == 2){
                $pdf = new PolizaFormatoT1B($poliza, $empresa);
            }
            $pdf->create(config('filesystems.disks.polizas_pdf.root'));if($i == 3)break;
        } 
        
        $zip_name = 'Polizas '.date("Ymdhis") . '.zip';
        $zipper = new Zipper;
        $files = glob(config('filesystems.disks.polizas_pdf.root').'/*');
        $zipper->make(config('filesystems.disks.polizas_zip.root'). '/' . $zip_name)->add($files)->close();

        Storage::disk('polizas_pdf')->delete(Storage::disk('polizas_pdf')->allFiles());
        return Storage::disk('polizas_zip')->download($zip_name);
        
    }

    private function busqueda($data){
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
           return $poliza;
        }catch (\Exception $e) {
            abort(500, "No tiene permiso de consultar la base de dato: ".$empresa->AliasBDD.".");
            throw $e;
        }
    }

}
