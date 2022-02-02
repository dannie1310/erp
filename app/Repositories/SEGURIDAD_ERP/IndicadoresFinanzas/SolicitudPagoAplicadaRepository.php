<?php


namespace App\Repositories\SEGURIDAD_ERP\IndicadoresFinanzas;

use App\Informes\Finanzas\PagosAnticipados;
use App\Models\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicada;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class SolicitudPagoAplicadaRepository extends Repository implements RepositoryInterface
{
    public function __construct(SolicitudPagoAplicada $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getIndicadorAplicadas()
    {
        $informe = PagosAnticipados::getIndicadorAplicadasCompletas();
        return $informe;
    }
}
