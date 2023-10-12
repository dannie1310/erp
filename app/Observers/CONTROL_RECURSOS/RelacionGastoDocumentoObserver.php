<?php

namespace App\Observers\CONTROL_RECURSOS;

use App\Models\CONTROL_RECURSOS\RelacionGastoDocumento;

class RelacionGastoDocumentoObserver
{
    /**
     * @param RelacionGastoDocumento $relacion
     */
    public function creating(RelacionGastoDocumento $relacion)
    {
        $relacion->idestado = 1;
        $relacion->modifico_estado = auth()->id();
        $relacion->registro = auth()->id();
    }

    public function created(RelacionGastoDocumento $relacion)
    {
        /**
         * Se realiza la función para agregar los estados a tablas adicionales, pero ya se realiza por medio de SP
         */
        //$relacion->agregarEstados();
    }

    public function updating(RelacionGastoDocumento $relacion)
    {
        if($relacion->getOriginal('idestado') != $relacion->idestado)
        {
            $relacion->modifico_estado = auth()->id();
        }
    }
}
