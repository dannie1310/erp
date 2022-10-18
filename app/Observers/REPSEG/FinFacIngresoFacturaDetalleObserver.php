<?php


namespace App\Observers\REPSEG;


use App\Models\REPSEG\FinFacIngresoFacturaDetalle;

class FinFacIngresoFacturaDetalleObserver
{
    public function creating(FinFacIngresoFacturaDetalle $detalle)
    {
        $detalle->timestamp = date('Y-m-d H:i:s');
    }
}
