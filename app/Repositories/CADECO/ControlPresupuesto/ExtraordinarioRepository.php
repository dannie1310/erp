<?php

namespace App\Repositories\CADECO\ControlPresupuesto;

use App\Models\CADECO\ControlPresupuesto\Extraordinario;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class ExtraordinarioRepository extends Repository implements RepositoryInterface
{
    public function __construct(Extraordinario $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->registrar($data);
    }

}
