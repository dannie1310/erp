<?php

namespace App\Repositories\MODULOSSAO\Proyectos;

use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\MODULOSSAO\Proyectos\Proyecto as Model;

class ProyectoRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }
}
