<?php

namespace App\Repositories\SEGURIDAD_ERP\InformeCostoVsCFDI;

use App\Models\SEGURIDAD_ERP\InformeCostoVsCFDI\CuentaCosto;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;


class CuentaCostoRepository extends Repository implements RepositoryInterface
{
    public function __construct(CuentaCosto $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

}
