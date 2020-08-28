<?php
/**
 * Created by PhpStorm.
 * User: JLopeza
 * Date: 23/06/2020
 * Time: 06:11 PM
 */

namespace App\Observers\CADECO\Catalogos;

use App\Models\CADECO\Catalogos\UnificacionProveedores;


class UnificacionProveedoresObserver
{
    /**
     * @param UnificacionProveedores $unificacion_proveedores
     *  @throws \Exception
     */
    public function creating(UnificacionProveedores $unificacion_proveedores)
    {
        $unificacion_proveedores->fecha_hora_registro = date('Y-m-d h:i:s');
        $unificacion_proveedores->usuario_registro = auth()->id();
    }
}