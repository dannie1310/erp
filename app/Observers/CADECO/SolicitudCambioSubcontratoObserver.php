<?php

namespace App\Observers\CADECO;

use App\Models\CADECO\SolicitudCambioSubcontrato;
use App\Models\CADECO\Transaccion;

class SolicitudCambioSubcontratoObserver extends TransaccionObserver
{
    /**
     * @param Transaccion $solicitud
     * @throws \Exception
     */
    public function creating(Transaccion $solicitud)
    {
        parent::creating($solicitud);
        $solicitud->tipo_transaccion = SolicitudCambioSubcontrato::TIPO;
        $solicitud->numero_folio = $solicitud->calcularFolio();
    }
}
