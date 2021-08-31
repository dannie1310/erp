<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 01:45 PM
 */

namespace App\Observers\CADECO\ControlPresupuesto;

use App\Models\CADECO\ControlPresupuesto\SolicitudCambio;


class VariacionVolumenObserver extends SolicitudCambioObserver
{
    /**
     * @param SolicitudCambio $variacion_volumen
     */
    public function creating(SolicitudCambio $variacion_volumen)
    {
        parent::creating($variacion_volumen);
        $variacion_volumen->numero_folio = $variacion_volumen->genera_folio();
        $variacion_volumen->id_tipo_orden = 4;
    }
}
