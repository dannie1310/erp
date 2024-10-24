<?php

namespace App\Repositories\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\PagoReembolsoPorSolicitud;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class PagoReembolsoPorSolicitudRepository extends Repository implements RepositoryInterface
{
    public function __construct(PagoReembolsoPorSolicitud $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function create(array $datos)
    {
        return $this->model->registrar($datos);
    }
}
