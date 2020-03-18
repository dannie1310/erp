<?php

namespace App\Services\CADECO\SubcontratosEstimaciones;

use App\Models\CADECO\SubcontratosEstimaciones\Penalizacion;
use App\Repositories\Repository;

class PenalizacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * PenalizacionService constructor
     * @param Penalizacion $model
     */
    public function __construct(Penalizacion $model)
    {
        $this->repository = new Repository($model);        
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function delete($data, $id)
    {
        return $this->repository->delete($data, $id);
    }

    public function store(array $data)
    {
        return $this->repository->create($data);
    }
}