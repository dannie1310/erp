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
        $penalizacion->estatus = 0;
    }

    /**
     * @param Penalizacion $penalizacion
     * @throws \Exception
     */
    public function deleting(Penalizacion $penalizacion)
    {
        $penalizacion->validarEstadoPenalizacion('eliminada');
    }    
}