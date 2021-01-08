<?php

namespace App\Observers\CADECO;

use App\Models\CADECO\SubcontratosCM\Solicitud;
use App\Models\CADECO\Transaccion;

class SolicitudObserver extends TransaccionObserver
{
    /**
     * @param Transaccion $solicitud
     * @throws \Exception
     */
    public function creating(Transaccion $solicitud)
    {
        parent::creating($solicitud);
        $solicitud->tipo_transaccion = Solicitud::TIPO;
        $solicitud->numero_folio = $solicitud->calcularFolio();
    }
}
