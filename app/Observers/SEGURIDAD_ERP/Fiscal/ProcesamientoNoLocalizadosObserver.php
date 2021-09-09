<?php

namespace App\Observers\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\Fiscal\ProcesamientoListaNoLocalizados;

class ProcesamientoNoLocalizadosObserver
{

    /**
     * @param ProcesamientoListaNoLocalizados $procesamiento
     */
    public function creating(ProcesamientoListaNoLocalizados $procesamiento){
        $procesamiento->id_usuario = auth()->id();
    }

}
