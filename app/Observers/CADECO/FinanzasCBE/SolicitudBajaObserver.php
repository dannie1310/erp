<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 08:25 PM
 */

namespace App\Observers\CADECO\FinanzasCBE;


use App\Models\CADECO\FinanzasCBE\SolicitudBaja;

class SolicitudBajaObserver
{
    /**
     * @param SolicitudBaja $solicitudBaja
     */
    public function creating(SolicitudBaja $solicitudBaja)
    {
        $solicitudBaja->validar();
        $solicitudBaja->numero_folio = $solicitudBaja->folio();
        $solicitudBaja->id_tipo_solicitud = 2;
        $solicitudBaja->fecha = date('Y-m-d H:i:s');
        $solicitudBaja->usuario_registra = auth()->id();
        $solicitudBaja->estado = 1;
    }

    public function created(SolicitudBaja $solicitudBaja)
    {
        $solicitudBaja->generaMovimiento(1);
    }
}