<?php


namespace App\Observers\REPSEG;


use App\Models\REPSEG\FinFacIngresoFactura;

class FinFacIngresoFacturaObserver
{

    /**
     * @param FinFacIngresoFactura $factura
     */
    public function creating(FinFacIngresoFactura $factura)
    {
        $factura->timestamp = date('Y-m-d H:i:s');
        $factura->registra = auth()->id();
        $factura->estado = 1;
        $factura->fecha_cobro = date('Y-m-d', strtotime($factura->fecha.'+ 1 month'));
    }
}
