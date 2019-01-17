<?php

namespace App\Repositories\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\PolizaMovimiento;
use App\Traits\RepositoryTrait;

class PolizaMovimientoRepository
{
    use RepositoryTrait;

    /**
     * @var PolizaMovimiento
     */
    private $model;

    /**
     * PolizaMovimientoRepository constructor.
     * @param PolizaMovimiento $model
     */
    public function __construct(PolizaMovimiento $model)
    {
        $this->model = $model;
    }

    public function update($data, $id)
    {
        $movimiento = $this->find($id);
        $movimiento->fill($data);
        $movimiento->save();

        return $movimiento;
    }

    public function create($data) {
        $movimiento = $this->model->create($data);

        return $movimiento;
    }
}