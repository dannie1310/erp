<?php

namespace App\Repositories\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\Fiscal\ProveedorREP;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class ProveedorREPRepository extends Repository implements RepositoryInterface
{
    public function __construct(ProveedorREP $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

}
