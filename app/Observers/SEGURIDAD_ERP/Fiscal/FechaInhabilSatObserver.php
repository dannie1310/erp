<?php

namespace App\Observers\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use App\Models\SEGURIDAD_ERP\Fiscal\FechaInhabilSat;

class FechaInhabilSatObserver
{
    public function updating(FechaInhabilSat $fecha_inhabil){
        $fecha_inhabil->usuario_cancelo = auth()->id();
        $fecha_inhabil->fecha_hora_cancelacion = date('Y-m-d H:i:s');
    }

    public function updated(FechaInhabilSat $fecha_inhabil){
        EFOS::editarFechaLimite();
    }

    public function creating(FechaInhabilSat $fechaInhabilSat)
    {
        $fechaInhabilSat->usuario_registro = auth()->id();
        $fechaInhabilSat->fecha_hora_registro = date('Y-m-d H:i:s');
        $fechaInhabilSat->estado = 1;
    }

    public function created(FechaInhabilSat $fechaInhabilSat)
    {
        EFOS::editarFechaLimite();
    }
}
