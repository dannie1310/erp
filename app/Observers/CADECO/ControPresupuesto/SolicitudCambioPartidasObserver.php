<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 01:45 PM
 */

namespace App\Observers\CADECO\ControlPresupuesto;


use App\Models\CADECO\ControlPresupuesto;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambioPartidas;

class SolicitudCambioPartidasObserver
{
    /**
     * @param SolicitudCambioPartidas $solicitud_cambio
     */
    public function creating(SolicitudCambioPartidas $solicitud_cambio_partidas)
    {
        
    }
}