<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\SolrecSemanaAnio;
use App\Repositories\Repository;

class SolRecSemanaAnioService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param SolrecSemanaAnio $model
     */
    public function __construct(SolrecSemanaAnio $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
