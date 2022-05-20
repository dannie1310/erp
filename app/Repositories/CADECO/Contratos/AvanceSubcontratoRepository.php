<?php


namespace App\Repositories\CADECO\Contratos;


use App\Models\CADECO\AvanceSubcontrato;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class AvanceSubcontratoRepository extends Repository implements RepositoryInterface
{
    public function __construct(AvanceSubcontrato $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function create(array $datos)
    {
        return $this->model->registrar($datos);
    }
}
