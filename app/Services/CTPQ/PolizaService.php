<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:41 AM
 */

namespace App\Services\CTPQ;
use App\Models\CTPQ\Empresa;
use App\Repositories\CTPQ\PolizaRepository as Repository;

use App\Models\CTPQ\Poliza;

class PolizaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * AlmacenService constructor.
     *
     * @param Almacen $model
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
        \Config::set('database.connections.cntpq.database',$empresa->AliasBDD);
        $poliza = $this->repository->show($id);
        if($poliza->Ejercicio<=2015){
            abort(500,"No se pueden editar pólizas de años anteriores a 2016");
        }
        return $this->repository->update($data, $id);
    }

    public function paginate($data)
    {
        $empresa = Empresa::find($data["id_empresa"]);
        \Config::set('database.connections.cntpq.database',$empresa->AliasBDD);
        $poliza = $this->repository;

        if (isset($data['ejercicio'])) {
            if($data['ejercicio'] != ""){
                $poliza->where([['Ejercicio', '=', $data['ejercicio']]]);
            }
        }

        if (isset($data['periodo'])) {
            if($data['periodo'] != ""){
                $poliza->where([['Periodo', '=', $data['periodo']]]);
            }
        }

        if (isset($data['numero_poliza'])) {
            if($data['numero_poliza'] != ""){
                $poliza->where([['Folio', '=', $data['numero_poliza']]]);
            }
        }

        if (isset($data['tipo_poliza'])) {
            if($data['tipo_poliza'] != ""){
                $poliza->where([['TipoPol', '=', $data['tipo_poliza']]]);
            }
        }

        if (isset($data['texto'])) {
            if($data['texto'] != ""){
                $poliza->where([['Concepto', 'LIKE', '%' . $data['texto'] . '%']]);
            }
        }

        return $poliza->paginate($data);
    }
}