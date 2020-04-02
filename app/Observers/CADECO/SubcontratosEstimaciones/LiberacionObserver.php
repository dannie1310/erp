<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 11/02/2020
 * Time: 20:05 PM
 */

namespace App\Observers\CADECO\SubcontratosEstimaciones;

use App\Models\CADECO\SubcontratosEstimaciones\Liberacion;

class LiberacionObserver {
    /**
     * @param Liberacion $liberacion
     * @throws \Exception
     */
    public function creating(Liberacion $liberacion)
    {
        $liberacion->validarImporteTotalALiberar();
        $liberacion->validarEstadoEstimacion('registrada');
        $liberacion->usuario = auth()->user()->usuario;
    }

    public function created(Liberacion $liberacion)
    {
        if($liberacion->retencion->importe == $liberacion->suma_liberado_por_retencion)
        {
            $liberacion->cerrarRetencion();
        }
    }

    public function deleting(Liberacion $liberacion)
    {
        $liberacion->validarEstadoEstimacion('eliminada');
    }

    public function deleted(Liberacion $liberacion)
    {
        if($liberacion->retencion->estatus == 1 || is_null($liberacion->retencion->estatus))
        {
            $liberacion->abrirRetencion();
        }
    }
}
