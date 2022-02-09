<?php


namespace App\Repositories\CADECO\Finanzas;

use App\Models\CADECO\Pago;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class PagoRepository extends Repository implements RepositoryInterface
{
    public function __construct(Pago $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function create(array $datos)
    {
        return $this->model->registrar($datos);
    }
}
