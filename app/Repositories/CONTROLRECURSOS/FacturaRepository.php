<?php

namespace App\Repositories\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\Factura;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class FacturaRepository extends Repository implements RepositoryInterface
{
    public function __construct(Factura $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }
}
