<?php

namespace App\Services\IGH;

use App\Repositories\Repository;
use App\Models\IGH\Ubicacion;

class UbicacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * Ubicacion constructor.
     * @param Ubicacion $model
     */
    public function __construct(Ubicacion $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data){
        return $this->repository->all($data);
    }
}