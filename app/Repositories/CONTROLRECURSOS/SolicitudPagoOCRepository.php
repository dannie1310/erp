<?php

namespace App\Repositories\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\SolicitudPagoOC;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class SolicitudPagoOCRepository extends Repository implements RepositoryInterface
{
    public function __construct(SolicitudPagoOC $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }
}
