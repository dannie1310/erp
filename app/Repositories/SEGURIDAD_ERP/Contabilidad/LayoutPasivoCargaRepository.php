<?php

namespace App\Repositories\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\LayoutPasivoCarga;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class LayoutPasivoCargaRepository extends Repository implements RepositoryInterface
{
    public function __construct(LayoutPasivoCarga $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }
}
