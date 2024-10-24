<?php

namespace App\Observers\CONTROL_RECURSOS;

use App\Models\CONTROL_RECURSOS\PagoAProveedor;
use App\Models\CONTROL_RECURSOS\SolCheque;

class PagoAProveedorObserver extends SolChequeObserver
{
    /**
     * @param PagoAProveedor $solCheque
     */
    public function creating(SolCheque $solCheque)
    {
        parent::creating($solCheque);
        $solCheque->Estatus = 10;
        $solCheque->IdTipoSolicitud = 6;
        $solCheque->IdTipoPago = 73;
    }

    public function updating(SolCheque $solCheque)
    {
        parent::updating($solCheque);
    }
}
