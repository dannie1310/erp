<?php

namespace App\Observers\CONTROL_RECURSOS;

use App\Models\CONTROL_RECURSOS\SolCheque;

class SolChequeObserver
{
    /**
     * @param SolCheque $solCheque
     */
    public function creating(SolCheque $solCheque)
    {
        $solCheque->Fecha = date('Y-m-d');
        $solCheque->Folio = $solCheque->getFolio($solCheque->Serie);
        $solCheque->registro_portal = 1;
        $solCheque->IdGenero = auth()->id();
    }

    public function updating(SolCheque $solCheque)
    {
        $solCheque->Modifica = auth()->id();
    }
}
