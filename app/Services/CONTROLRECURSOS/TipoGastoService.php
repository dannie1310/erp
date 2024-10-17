<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\TipoGasto;
use App\Repositories\Repository;

class TipoGastoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param TipoGasto $model
     */
    public function __construct(TipoGasto $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
