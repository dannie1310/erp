<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 11/02/2020
 * Time: 20:03 PM
 */

namespace App\Observers\CADECO\subcontratosEstimaciones;

use App\Models\CADECO\SubcontratosEstimaciones\Retencion;

class RetencionObserver {

    /**
     * @param Retencion $retencion
     * @throws \Exception
     */
    public function creating(Retencion $retencion)
    {
        $retencion->validarEstadoEstimacion('registrada');
        $retencion->validarTotalRetencion();
        $retencion->estatus = 0;
    }

    /**
     * @param Retencion $retencion
     * @throws \Exception
     */
    public function created(Retencion $retencion)
    {
        $retencion->estimacion->recalculaDatosGenerales();
    }

    /**
     * @param Retencion $retencion
     * @throws \Exception
     */
    public function deleting(Retencion $retencion)
    {
        if (count($retencion->liberaciones) > 0)
        {
            abort(403, 'La retención no puede ser eliminada porque se encuentra liberada.');
        }
        $retencion->validarEstadoEstimacion('eliminada');
    }

    /**
     * @param Retencion $retencion
     * @throws \Exception
     */
    public function deleted(Retencion $retencion)
    {
        $retencion->estimacion->recalculaDatosGenerales();
    }
}
