<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 07/08/2020
 * Time: 03:58 PM
 */

namespace App\Repositories\SEGURIDAD_ERP\PadronProveedores;

use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgEspecialidad as Model;

class EspecialidadRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }
}