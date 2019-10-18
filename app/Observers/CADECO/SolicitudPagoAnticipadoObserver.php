<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 04:56 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\CADECO\Transaccion;

class SolicitudPagoAnticipadoObserver extends TransaccionObserver
{
    /**
     * @param SolicitudPagoAnticipado $solicitud
     * @throws \Exception
     */
    public function creating(Transaccion $solicitud)
    {
        parent::creating($solicitud);
        $solicitud->validarAntecedente();
        $solicitud->tipo_transaccion = 72;
        $solicitud->opciones = 327681;
        $solicitud->estado = 0;
    }

    public function created(SolicitudPagoAnticipado $solicitud)
    {
        $solicitud->generaTransaccionRubro();
    }
}