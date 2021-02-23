<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 24/06/2020
 * Time: 02:00 PM
 */

namespace App\Repositories\SEGURIDAD_ERP\Fiscal;


use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\Fiscal\EmpresaFacturera as Model;

class EmpresaFactureraRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

}