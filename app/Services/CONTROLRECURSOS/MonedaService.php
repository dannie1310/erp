<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\CtgMoneda;
use App\Repositories\Repository;

class MonedaService
{

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param CtgMoneda $model
     */
    public function __construct(CtgMoneda $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
