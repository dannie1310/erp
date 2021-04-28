<?php

namespace App\Observers\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use App\Models\SEGURIDAD_ERP\Fiscal\FechaInhabilSat;

class FechaInhabilSatObserver
{
    public function updated(FechaInhabilSat $fecha_inhabil){
        $fecha_inhabil->usuario_cancelo = auth()->id();
        $fecha_inhabil->fecha_hora_cancelacion = date('Y-m-d H:i:s');
        $fecha_inhabil->save();
        EFOS::editarFechaLimite();
    }
}
