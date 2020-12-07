<?php


namespace App\Observers\ACARREOS;


use App\Models\ACARREOS\VolumenDetalle;

class VolumenDetalleObserver
{
    /**
     * @param VolumenDetalle $volumenDetalle
     */
    public function creating(VolumenDetalle $volumenDetalle)
    {
        $volumenDetalle->fecha_hora_registro = date('Y-m-d H:i:s');
    }
}
