<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\CajaChica;
use App\Repositories\Repository;

class CajaChicaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param CajaChica $model
     */
    public function __construct(CajaChica $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
