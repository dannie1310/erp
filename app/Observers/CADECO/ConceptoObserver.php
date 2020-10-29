<?php

namespace App\Observers\CADECO;

use App\Models\CADECO\Concepto;

class ConceptoObserver extends TransaccionObserver
{
    public function updating(Concepto $concepto)
    {
        if(!($concepto->getOriginal("clave") !=  $concepto->clave)) {

        }
    }
}
