<?php


namespace App\Observers\REPSEG;


use App\Models\REPSEG\FinDimTipoIngreso;

class FinDimTipoIngresoObserver
{
    public function creating(FinDimTipoIngreso $tipo)
    {
        $tipo->timestamp = date('Y-m-d H:i:s');
        $tipo->estado = 1;
    }
}
