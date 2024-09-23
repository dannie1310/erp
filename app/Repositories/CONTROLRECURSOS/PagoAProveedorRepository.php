<?php

namespace App\Repositories\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\PagoAProveedor;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class PagoAProveedorRepository extends Repository implements RepositoryInterface
{
    public function __construct(PagoAProveedor $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function create(array $datos)
    {
        return $this->model->registrar($datos);
    }

}
