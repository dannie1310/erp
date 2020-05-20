<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 20/05/2020
 * Time: 05:09 PM
 */

namespace App\Observers\SEGURIDAD_ERP\PolizasCtpqIncidentes;


use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;

class DiferenciaObserver
{
    /**
     * @param AuditoriaPermisoRol $auditoriaPermisoRol
     */
    public function creating(Diferencia $diferencia)
    {
        $diferencia_pre = Diferencia::activos()->where("id_poliza", $diferencia->id_poliza)
            ->where("base_datos_revisada",$diferencia->base_datos_revisada)
            ->where("base_datos_referencia",$diferencia->base_datos_referencia)
            ->where("id_tipo",$diferencia->id_tipo)
            ->where("tipo_busqueda",$diferencia->tipo_busqueda)
            ->first();
        if($diferencia_pre)
        {
            $diferencia_pre->activo= 0;
            $diferencia_pre->save();
        }
    }

}