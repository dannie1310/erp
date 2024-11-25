<?php

namespace App\Repositories\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\Comprobacion;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class ComprobacionRepository extends Repository implements RepositoryInterface
{
    public function __construct(Comprobacion $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function registrar($data)
    {
        return $this->model->registrar($data);
    }
}
