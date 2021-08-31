<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 01:45 PM
 */

namespace App\Observers\CADECO\ControlPresupuesto;

use App\Facades\Context;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambio;

class SolicitudCambioObserver
{
    /**
     * @param SolicitudCambio $solicitud_cambio
     */
    public function creating(SolicitudCambio $solicitud_cambio)
    {
        $solicitud_cambio->id_solicita = auth()->id();
        $solicitud_cambio->id_obra = Context::getIdObra();
        $solicitud_cambio->id_estatus = 1;
    }
}
