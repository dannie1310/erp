<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 22/04/2020
 * Time: 02:29 PM
 */

namespace App\Observers\CADECO\Compras;

use App\Models\CADECO\Compras\AsignacionProveedoresPartida;

class AsignacionProveedoresPartidaObserver
{
    /**
     * @param AsignacionProveedores $asignacion_proveedor
     */
    public function creating(AsignacionProveedoresPartida $asignacion_proveedor_partida)
    {
        $asignacion_proveedor_partida->registro = auth()->id();
    }
}