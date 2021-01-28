<?php


namespace App\Repositories\CADECO\Contratos\Asignacion;


use App\Models\CADECO\Subcontratos\AsignacionContratista;
use App\Repositories\RepositoryInterface;

class Repository extends \App\Repositories\Repository  implements RepositoryInterface
{
    public function __construct(AsignacionContratista $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function registrar($data)
    {
        return $this->model->registrar($data);
    }
}
