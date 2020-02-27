<?php


namespace App\Observers\CADECO;

use App\Models\CADECO\Unidad;
use App\Models\CADECO\UnidadComplemento;

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
         UnidadComplemento::create([
             'unidad' => strtoupper($unidad->unidad)
         ]);
     }

}