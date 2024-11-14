<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\Comprobacion;
use App\Repositories\Repository;

class ComprobacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param Comprobacion $model
     */
    public function __construct(Comprobacion $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }
}
