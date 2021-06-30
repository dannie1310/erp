<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 01:45 PM
 */

namespace App\Observers\CADECO\ControlPresupuesto;


use App\Models\CADECO\ControlPresupuesto;
use App\Models\CADECO\ControlPresupuesto\VariacionVolumen;
use App\Observers\CADECO\ControlPresupuesto\SolicitudCambioObserver;

class VariacionVolumenObserver extends SolicitudCambioObserver
{
    /**
     * @param VariacionVolumen $variacion_volumen
     */
    public function creating(VariacionVolumen $variacion_volumen)
    {
        parent::creating($variacion_volumen);   
        $variacion_volumen->numero_folio = $variacion_volumen->genera_folio();
        $variacion_volumen->id_tipo_orden = 4;
        // $solicitud_cambio->fecha_solicitud = date('Y-m-d h:i:s');
    }
}