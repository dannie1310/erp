<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\VwUbicacionRelacion;
use App\Repositories\Repository;

class UbicacionRelacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param VwUbicacionRelacion $model
     */
    public function __construct(VwUbicacionRelacion $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
