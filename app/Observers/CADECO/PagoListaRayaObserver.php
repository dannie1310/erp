<?php


namespace App\Observers\CADECO;


use App\Models\CADECO\Pago;
use App\Models\CADECO\Transaccion;

class PagoListaRayaObserver extends PagoObserver
{
    /**
     * @param Transaccion $pago
     * @throws \Exception
     *
     */
    public function creating(Transaccion $pago)
    {
        parent::creating($pago);
        $pago->tipo_transaccion = 82;
        $pago->opciones = 65537;
    }

    public function created(Pago $pago){
        parent::created($pago);
        $pago->solicitud->cambiosAlPagar($pago);
    }
}
