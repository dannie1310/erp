<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 05:02 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\Sucursal;

class SucursalObserver
{
    /**
     * @param Sucursal $sucursal
     */
    public function creating(Sucursal $sucursal)
    {
        $sucursal->UsuarioRegistro = auth()->id();
        $sucursal->descripcion = mb_strtoupper($sucursal->descripcion);
        $sucursal->direccion = mb_strtoupper($sucursal->direccion);
        $sucursal->ciudad = mb_strtoupper($sucursal->ciudad);
        $sucursal->estado = mb_strtoupper($sucursal->estado);
        $sucursal->contacto = mb_strtoupper($sucursal->contacto);
    }

    /**
     * @param Sucursal $sucursal
     */
    public function deleting(Sucursal $sucursal){
        $sucursal->validarEliminacionSucursal();
    }
}