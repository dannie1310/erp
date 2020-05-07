<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 22/04/2020
 * Time: 02:27 PM
 */

namespace App\Observers\CADECO\Compras;

use App\Models\CADECO\Compras\AsignacionProveedores;


class AsignacionProveedoresObserver
{
    /**
     * @param AsignacionProveedores $asignacion_proveedor
     */
    public function creating(AsignacionProveedores $asignacion_proveedor)
    {
        $asignacion_proveedor->registro = auth()->id();
    }
}