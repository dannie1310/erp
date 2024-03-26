<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\Proveedor;
use App\Repositories\Repository;

class ProveedorService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param Proveedor $model
     */
    public function __construct(Proveedor $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
