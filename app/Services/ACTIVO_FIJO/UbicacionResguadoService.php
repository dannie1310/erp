<?php

namespace App\Services\ACTIVO_FIJO;

use App\Models\SCI\VwUbicacionResguado;
use App\Repositories\Repository;

class UbicacionResguadoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param VwListaDepartamento $model
     */
    public function __construct(VwUbicacionResguado $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
