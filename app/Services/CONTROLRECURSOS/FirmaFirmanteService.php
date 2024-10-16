<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\FirmaFirmante;
use App\Repositories\Repository;

class FirmaFirmanteService
{

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param FirmaFirmante $model
     */
    public function __construct(FirmaFirmante $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
