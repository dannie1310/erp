<?php


namespace App\Observers\CADECO\SubcontratosEstimaciones;

use App\Models\CADECO\SubcontratosEstimaciones\Penalizacion;

class PenalizacionObserver
{
    
    public function creating(Penalizacion $penalizacion)
    {
        $penalizacion->validarEstadoPenalizacion('registrada');
        $penalizacion->validarTotalPenalizacion();
        $penalizacion->estatus = 0;
    }

    public function created(Penalizacion $penalizacion)
    {
        $penalizacion->estimacion->recalculaDatosGenerales();
    }

    public function deleting(Penalizacion $penalizacion)
    {
        $penalizacion->validarEstadoPenalizacion('eliminada');
    }   

    public function deleted(Penalizacion $penalizacion)
    {
        $penalizacion->estimacion->recalculaDatosGenerales();
    }
}