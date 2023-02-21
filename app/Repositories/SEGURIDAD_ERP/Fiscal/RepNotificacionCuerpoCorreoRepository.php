<?php

namespace App\Repositories\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\Fiscal\ProveedorREP;
use App\Models\SEGURIDAD_ERP\Fiscal\RepNotificacionCuerpoCorreo;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class RepNotificacionCuerpoCorreoRepository extends Repository implements RepositoryInterface
{
    public function __construct(RepNotificacionCuerpoCorreo $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

}
