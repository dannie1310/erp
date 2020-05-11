<?php


namespace App\Repositories\CADECO\Compras\Asignacion;


use App\Repositories\RepositoryInterface;
use App\Models\CADECO\Compras\AsignacionProveedores;

class Repository extends \App\Repositories\Repository  implements RepositoryInterface
{
    public function __construct(AsignacionProveedores $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }
}
