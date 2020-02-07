<?php


namespace App\Observers\CADECO;

use App\Models\CADECO\Unidad;

class UnidadObserver
{
    /**
     * @param Unidad $unidad
     */

     public function creating(Unidad $unidad)
     {
         $unidad->validarUnidadExistente();
         $unidad->unidad = strtoupper($unidad->unidad);
         $unidad->descripcion = strtoupper($unidad->descripcion);
         
     }
}