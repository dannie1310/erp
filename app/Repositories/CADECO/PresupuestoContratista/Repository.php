<?php

namespace App\Repositories\CADECO\PresupuestoContratista;

use App\Models\CADECO\PresupuestoContratista;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * RepositoryInterface constructor.
     * @param Requisicion $model
     */
    public function __construct(PresupuestoContratista $model)
    {
        $this->model = $model;
    }

    public function descargaLayout($id)
    {
        return $this->model->descargaLayout($id);
    }

    public function create(array $data)
    {
        return $this->model->crear($data);        
    }
}