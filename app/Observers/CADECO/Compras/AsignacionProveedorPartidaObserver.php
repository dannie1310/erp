<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 22/04/2020
 * Time: 02:29 PM
 */

namespace App\Observers\CADECO\Compras;

use App\Models\CADECO\Compras\AsignacionProveedorPartida;

class AsignacionProveedorPartidaObserver
{
    /**
     * @param AsignacionProveedores $asignacion_proveedor
     */
    public function creating(AsignacionProveedorPartida $asignacion_proveedor_partida)
    {
        $asignacion_proveedor_partida->registro = auth()->id();
    }

    public function created(AsignacionProveedorPartida $asignacion_proveedor_partida)
    {
        /**
         * Cambiar estado de la cotización a: En asignación
         */
        $asignacion_proveedor_partida->cotizacionCompra->estado = 2;
        $asignacion_proveedor_partida->cotizacionCompra->save();
    }

    public function deleted(AsignacionProveedorPartida $asignacion_proveedor_partida)
    {
        /**
         * Cambiar estado de la cotización a: Registrada
         */
        $otras = AsignacionProveedorPartida::where("id_transaccion_cotizacion","=",$asignacion_proveedor_partida->id_transaccion_cotizacion)
            ->where("id","!=",$asignacion_proveedor_partida->id)->get();
        if(count($otras)==0)
        {
            $asignacion_proveedor_partida->cotizacionCompra->estado = 1;
            $asignacion_proveedor_partida->cotizacionCompra->save();
        }
    }
}
