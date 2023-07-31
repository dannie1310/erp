<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\SolicitudPagoOC;
use App\Repositories\CONTROLRECURSOS\SolicitudPagoOCRepository as Repository;

class SolicitudPagoOCService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param SolicitudPagoOC $model
     */
    public function __construct(SolicitudPagoOC $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }
}
