<?php

namespace App\Repositories\CTPQ;

use App\Models\CTPQ\Cuenta;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CuentaRepository extends Repository implements RepositoryInterface
{
    public function __construct(Cuenta $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getCuentasPasivo($id_empresa)
    {
       return Cuenta::cuentasPasivo($id_empresa)->get();
    }
}
