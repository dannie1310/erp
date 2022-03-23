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

    public function porAutorizar()
    {
        $firmante = auth()->user()->firmante;
        $nivel_autorizacion = $firmante->id_nivel_autorizacion;

        $solicitudes = SolicitudPagoAutorizacion::withoutGlobalScopes()->select('EsquemaAutorizacion.transacciones.*')
            ->join('EsquemaAutorizacion.autorizaciones_requeridas', 'autorizaciones_requeridas.id_transaccion_general', '=', 'transacciones.id')
            ->whereRaw("autorizaciones_requeridas.id_nivel_requerido = $nivel_autorizacion")
            ->whereRaw("autorizaciones_requeridas.estado = 0")
            ->whereRaw("transacciones.estado >= 0")
            ->get();

        return $solicitudes;
    }
}
