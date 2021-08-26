<?php

namespace App\Repositories\SEGURIDAD_ERP\PadronProveedores;

use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivo as Model;

class InvitacionArchivoRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

}
