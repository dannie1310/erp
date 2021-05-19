<?php

namespace App\Repositories\SEGURIDAD_ERP\Fiscal;

use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS as Model;


class EfosRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function calcularFechasLimite()
    {
        $this->model->editarFechaLimite();
    }

}
