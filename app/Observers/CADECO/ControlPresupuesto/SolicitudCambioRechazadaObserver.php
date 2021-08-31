<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 01:45 PM
 */

namespace App\Observers\CADECO\ControlPresupuesto;


use App\Models\CADECO\ControlPresupuesto;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambioRechazada;

class SolicitudCambioRechazadaObserver
{
    /**
     * @param SolicitudCambioRechazada $solicitud_cambio
     */
    public function creating(SolicitudCambioRechazada $solicitud_cambio_rechazada)
    {
        $solicitud_cambio_rechazada->id_rechazo = auth()->id();
    }
}