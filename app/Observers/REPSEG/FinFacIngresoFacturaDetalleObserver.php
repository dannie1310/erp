<?php


namespace App\Observers\REPSEG;


use App\Models\REPSEG\FinFacIngresoFacturaDetalle;

class FinFacIngresoFacturaDetalleObserver
{
    public function creating(FinFacIngresoFacturaDetalle $detalle)
    {
        $detalle->timestamp = date('Y-m-d H:i:s');
        if($detalle->antes_iva)
        {
            $detalle->antes_iva = 1;
        }else{
            $detalle->antes_iva = 0;
        }
    }
}
