<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 11/03/2020
 * Time: 03:47 PM
 */

namespace App\Observers\SEGURIDAD_ERP\Contabilidad;


use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudEdicion;

class SolicitudEdicionObserver
{
    /**
     * @param CargaCFDSAT $log
     */
    public function creating(SolicitudEdicion $solicitud)
    {
        $solicitud->usuario_registro = auth()->id();
        $solicitud->fecha_hora_registro = date("Y-m-d H:i:s");
        $solicitud->numero_folio = SolicitudEdicion::getFolio();
    }
}