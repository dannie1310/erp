<?php


namespace App\Observers\CADECO\SubcontratosEstimaciones;

use App\Models\CADECO\SubcontratosEstimaciones\Penalizacion;

class PenalizacionObserver
{
    /**
     * @param Penalizacion $penalizacion
     * @throws \Exception
     */
    public function creating(Penalizacion $penalizacion)
    {
        $penalizacion->validarEstadoPenalizacion('registrada');
        $penalizacion->validarTotalPenalizacion();
        $penalizacion->estatus = 0;
    }

    /**
     * @param Penalizacion $penalizacion
     * @throws \Exception
     */
    public function created(Penalizacion $penalizacion)
    {
        $penalizacion->estimacion->recalculaDatosGenerales();
    }

    /**
     * @param Penalizacion $penalizacion
     * @throws \Exception
     */
    public function deleting(Penalizacion $penalizacion)
    {
        $penalizacion->validarEstadoPenalizacion('eliminada');
    }    

    /**
     * @param Penalizacion $penalizacion
     * @throws \Exception
     */
    public function deleted(Penalizacion $penalizacion)
    {
        $penalizacion->estimacion->recalculaDatosGenerales();
    }
}