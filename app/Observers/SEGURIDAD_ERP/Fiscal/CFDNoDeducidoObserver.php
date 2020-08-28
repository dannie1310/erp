<?php


namespace App\Observers\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Fiscal\CFDNoDeducido;

class CFDNoDeducidoObserver
{
    /**
     * @param CFDNoDeducido $partida
     */
    public function creating(CFDNoDeducido $partida)
    {
        $partida->validar();
        $partida->registro = auth()->id();
        $partida->fecha_hora_registro = date('Y-m-d H:i:s');
        $partida->estado = 7;
    }

    public function created(CFDNoDeducido $partida)
    {
        $partida->cfdSat->update([
            'estado' => 7
        ]);
    }
}
