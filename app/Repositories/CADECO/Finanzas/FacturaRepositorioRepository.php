<?php

namespace App\Repositories\CADECO\Finanzas;

use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class FacturaRepositorioRepository extends Repository implements RepositoryInterface
{
    public function __construct(FacturaRepositorio $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

}
