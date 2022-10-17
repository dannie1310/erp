<?php


namespace App\Repositories\REPSEG;


use App\Models\REPSEG\FinFacIngresoFactura;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class FacturaRepository extends Repository implements RepositoryInterface
{
    public function __construct(FinFacIngresoFactura $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function create(array $datos)
    {
        return $this->model->registrar($datos);
    }
}
