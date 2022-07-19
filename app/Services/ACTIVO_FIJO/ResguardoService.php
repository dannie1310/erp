<?php

namespace App\Services\ACTIVO_FIJO;

use App\Models\ACTIVO_FIJO\Resguardo;
use App\Repositories\ACTIVO_FIJO\ResguardoRepository as Repository;

class ResguardoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * Resguardo constructor.
     * @param Resguardo $model
     */
    public function __construct(Resguardo $model)
    {
        $this->repository = new Repository($model);
    }

    public function listaResguardos($data){
        return $this->repository->getListaResguardos($data);
    }
}