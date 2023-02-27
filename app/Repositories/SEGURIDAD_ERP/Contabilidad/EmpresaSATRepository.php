<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 26/02/2020
 * Time: 03:32 PM
 */

namespace App\Repositories\SEGURIDAD_ERP\Contabilidad;

use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;


class EmpresaSATRepository extends Repository implements RepositoryInterface
{
    public function __construct(EmpresaSAT $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function cargarCuentas($id_empresa, $cuentas)
    {
        $empresa = $this->model->find($id_empresa);
        return $empresa->cargarCuentas($cuentas);
    }

}
