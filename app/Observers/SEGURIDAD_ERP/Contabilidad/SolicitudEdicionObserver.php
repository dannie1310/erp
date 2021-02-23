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
        $solicitud->id_usuario_registro = auth()->id();
        $solicitud->fecha_hora_registro = date("Y-m-d H:i:s");
        $solicitud->numero_folio = SolicitudEdicion::getFolio();
    }

    public function updating(SolicitudEdicion $solicitud)
    {
        if($solicitud->getOriginal("estado")==0 && $solicitud->estado == 1){
            $solicitud->id_usuario_autorizo = auth()->id();
            $solicitud->fecha_hora_autorizacion = date("Y-m-d H:i:s");
        }

        if($solicitud->getOriginal("estado")==0 && $solicitud->estado == -1){
            $solicitud->id_usuario_rechazo = auth()->id();
            $solicitud->fecha_hora_rechazo = date("Y-m-d H:i:s");
        }
        if($solicitud->getOriginal("estado")==1 && $solicitud->estado == 2){
            $solicitud->id_usuario_aplico = auth()->id();
            $solicitud->fecha_hora_aplicacion = date("Y-m-d H:i:s");
        }
    }
}