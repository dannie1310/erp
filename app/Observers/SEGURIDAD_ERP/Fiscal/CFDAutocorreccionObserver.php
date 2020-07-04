<?php


namespace App\Observers\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Fiscal\CFDAutocorreccion;

class CFDAutocorreccionObserver
{
    /**
     * @param CFDAutocorreccion $partida
     */
    public function creating(CFDAutocorreccion $partida)
    {
        $partida->registro = auth()->id();
        $partida->fecha_hora_registro = date('Y-m-d H:i:s');
        $partida->estado = 0;
    }
}
