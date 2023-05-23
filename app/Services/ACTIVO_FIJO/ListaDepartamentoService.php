<?php

namespace App\Services\ACTIVO_FIJO;

use App\Models\SCI\VwListaDepartamento;
use App\Repositories\Repository;

class ListaDepartamentoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param VwListaDepartamento $model
     */
    public function __construct(VwListaDepartamento $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
