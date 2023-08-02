<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\Serie;
use App\Repositories\Repository;

class SerieService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param Serie $model
     */
    public function __construct(Serie $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
