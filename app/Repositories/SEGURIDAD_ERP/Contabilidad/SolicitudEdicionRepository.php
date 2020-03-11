<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 11/03/2020
 * Time: 03:55 PM
 */

namespace App\Repositories\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicion;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class SolicitudEdicionRepository extends Repository implements RepositoryInterface
{
    public function __construct(SolicitudEdicion $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function registrar(array $datos)
    {
        return $this->model->registrar($datos);
    }

}