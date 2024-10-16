<?php

namespace App\Observers\CONTROL_RECURSOS;

use App\Models\CONTROL_RECURSOS\PagoReembolsoPorSolicitud;
use App\Models\CONTROL_RECURSOS\SolCheque;

class PagoReembolsoPorSolicitudObserver extends SolChequeObserver
{
    /**
     * @param PagoReembolsoPorSolicitud $solCheque
     */
    public function creating(SolCheque $solCheque)
    {
        parent::creating($solCheque);
        $solCheque->Estatus = 30;
        $solCheque->IdTipoSolicitud = 4;
        $solCheque->IdTipoPago = 6;
    }
}
