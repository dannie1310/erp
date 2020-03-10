<?php

namespace App\Observers\CADECO;

use App\Models\CADECO\UnidadComplemento;

class UnidadComplementoObserver
{
    /**
     * @param UnidadComplemento $complemento
     */

     public function creating(UnidadComplemento $complemento)
     {
         $complemento->IdUsuario = auth()->id();
     }
}