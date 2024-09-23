<?php

namespace App\Repositories\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\ReembolsoPagoAProveedor;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class ReembolsoPagoAProveedorRepository extends Repository implements RepositoryInterface
{
    public function __construct(ReembolsoPagoAProveedor $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function create(array $datos)
    {
        return $this->model->registrar($datos);
    }
}
