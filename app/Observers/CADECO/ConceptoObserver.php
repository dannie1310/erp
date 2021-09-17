<?php

namespace App\Observers\CADECO;

use App\Facades\Context;
use App\Models\CADECO\Concepto;

class ConceptoObserver
{
    public function creating(Concepto $concepto)
    {
        $concepto->id_obra = Context::getIdObra();
        $concepto->consecutivo_extraordinario = $concepto->calcularConsecutivoExtraordinario();
    }

}
