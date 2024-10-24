<?php

namespace App\Repositories\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\ReembolsoGastoSol;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class ReembolsoGastoSolRepository extends Repository implements RepositoryInterface
{
    public function __construct(ReembolsoGastoSol $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function registrar($data)
    {
        return $this->model->registrar($data);
    }
}
