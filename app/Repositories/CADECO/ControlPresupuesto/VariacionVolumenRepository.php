<?php

namespace App\Repositories\CADECO\ControlPresupuesto;

use App\Facades\Context;
use App\Models\CADECO\ControlPresupuesto\VariacionVolumen;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\DB;

class VariacionVolumenRepository extends Repository implements RepositoryInterface
{
    public function __construct(VariacionVolumen $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->registrar($data);
    }

}
