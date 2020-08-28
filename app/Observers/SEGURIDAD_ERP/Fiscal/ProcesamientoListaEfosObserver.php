<?php


namespace App\Observers\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\Fiscal\ProcesamientoListaEfos;

class ProcesamientoListaEfosObserver
{
    /**
     * @param ProcesamientoListaEfos $efos
     */

    public function creating(ProcesamientoListaEfos $procesamiento_lista_efo){
        $procesamiento_lista_efo->id_usuario = auth()->id();
    }

}
