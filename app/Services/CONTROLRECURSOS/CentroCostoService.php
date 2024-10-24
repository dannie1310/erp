<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\CentroCosto;
use App\Repositories\Repository;

class CentroCostoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param CentroCosto $model
     */
    public function __construct(CentroCosto $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
