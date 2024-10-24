<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\TipoGastoComp;
use App\Repositories\Repository;

class TipoGastoCompService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param TipoGastoComp $model
     */
    public function __construct(TipoGastoComp $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
