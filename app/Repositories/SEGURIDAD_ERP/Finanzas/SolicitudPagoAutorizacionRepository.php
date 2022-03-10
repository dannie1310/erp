<?php
namespace App\Repositories\SEGURIDAD_ERP\Finanzas;

use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacion;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class SolicitudPagoAutorizacionRepository extends Repository implements RepositoryInterface
{
    public function __construct(SolicitudPagoAutorizacion $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }
}
