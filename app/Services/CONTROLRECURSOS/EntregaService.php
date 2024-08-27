<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\Entrega;
use App\Repositories\Repository;

class EntregaService
{

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param Entrega $model
     */
    public function __construct(Entrega $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
