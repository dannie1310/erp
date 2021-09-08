<?php

namespace App\Observers\CADECO\ControlPresupuesto;

use App\Models\CADECO\ControlPresupuesto\SolicitudCambio;


class ExtraordinarioObserver extends SolicitudCambioObserver
{
    /**
     * @param SolicitudCambio $variacion_volumen
     */
    public function creating(SolicitudCambio $extraordinario)
    {
        parent::creating($extraordinario);
        $extraordinario->numero_folio = $extraordinario->genera_folio();
        $extraordinario->id_tipo_orden = 3;
    }
}
