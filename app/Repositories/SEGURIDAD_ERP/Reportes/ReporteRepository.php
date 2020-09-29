<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 02/04/2020
 * Time: 01:03 PM
 */

namespace App\Repositories\SEGURIDAD_ERP\Reportes;

use App\Models\SEGURIDAD_ERP\Reportes\Reporte as Model;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class ReporteRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

}