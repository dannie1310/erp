<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\Empresa;
use App\Repositories\Repository;

class EmpresaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param Empresa $model
     */
    public function __construct(Empresa $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
