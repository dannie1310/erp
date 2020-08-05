<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:41 AM
 */

namespace App\Services\CTPQ;
use App\Models\CTPQ\Poliza;
use App\Models\CTPQ\Empresa;
use App\Models\CTPQ\TipoPoliza;
use App\PDF\CTPQ\PolizaFormatoT1;
use App\PDF\CTPQ\PolizaFormatoT1A;

use App\PDF\CTPQ\PolizaFormatoT1B;
use Illuminate\Support\Facades\DB;
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
                    $poliza = $poliza->where([['Concepto','like', '%'.strtoupper($data['concepto']).'%']]);
                }
            }

            if (isset($data['tipo'])) {
                if($data['tipo'] != '') {
                    $tipos = TipoPoliza::where('Nombre', 'like', '%'.ucfirst(request('tipo')).'%')->get();
                    foreach ($tipos as $a) {
                        $poliza = $poliza->whereOr([['TipoPol', '=', $a->Id]]);
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
}
