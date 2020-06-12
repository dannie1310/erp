<?php


namespace App\Observers\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\PagoEliminadoLog;

class PagoEliminadoLogObserver
{
    /**
     * @param PagoEliminadoLog $pago
     */
    public function creating(PagoEliminadoLog $pago)
    {
        $pago->usuario_elimina = auth()->id();
        $pago->fecha_eliminacion = date('Y-m-d H:i:s');
    }
}
