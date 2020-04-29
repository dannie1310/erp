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
        if($penalizacion->liberaciones->first())
        {
            abort(403, 'La penalización no puede ser eliminada porque se encuentra liberada.');
        }
        $penalizacion->validarEstadoPenalizacion('eliminada');
    }   

    public function deleted(Penalizacion $penalizacion)
    {
        $penalizacion->estimacion->recalculaDatosGenerales();
    }
}