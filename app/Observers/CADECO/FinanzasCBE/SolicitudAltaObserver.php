<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 08:21 PM
 */

namespace App\Observers\CADECO\FinanzasCBE;


use App\Models\CADECO\FinanzasCBE\SolicitudAlta;

class SolicitudAltaObserver
{
    /**
     * @param SolicitudAlta $solicitudAlta
     */
    public function creating(SolicitudAlta $solicitudAlta)
    {
        $solicitudAlta->validar();
        $solicitudAlta->numero_folio = $solicitudAlta->folio();
        $solicitudAlta->id_tipo_solicitud = 1;
        $solicitudAlta->fecha = date('Y-m-d H:i:s');
        $solicitudAlta->usuario_registra = auth()->id();
        $solicitudAlta->estado = 1;
    }

    public function created(SolicitudAlta $solicitudAlta)
    {
        $solicitudAlta->generaMovimiento(1);
    }
}