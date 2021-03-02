<?php


namespace App\Repositories\SEGURIDAD_ERP\Finanzas;


use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDI as Model;

class SolicitudRecepcionCFDIRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function registrar($data)
    {
        return $this->model->registrar($data);
    }

}
