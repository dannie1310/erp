<?php

namespace App\Observers\REPSEG;

use App\Models\REPSEG\FinDimIngresoPartida;

class FinDimIngresoPartidaObserver
{
    public function creating(FinDimIngresoPartida $partida)
    {
        $partida->estado = 1;
        if($partida->operador == '-')
        {
            $partida->nombre_operador = 'MENOS';
        }
        if($partida->operador == '+')
        {
            $partida->nombre_operador = 'MAS';
        }
    }
}
