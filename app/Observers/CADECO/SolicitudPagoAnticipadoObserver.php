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

class SolicitudPagoAnticipadoObserver
{
    /**
     * @param SolicitudPagoAnticipado $solicitud
     * @throws \Exception
     */
    public function creating(SolicitudPagoAnticipado $solicitud)
    {
        if (!$solicitud->validaTipoAntecedente()) {
            throw New \Exception('La transacción antecedente no es válida');
        }
        $solicitud->validarAntecedente();
        $solicitud->tipo_transaccion = 72;
        $solicitud->opciones = 327681;
        $solicitud->estado = 0;
        $solicitud->id_usuario = auth()->id();
        $solicitud->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
        $solicitud->FechaHoraRegistro = date('Y-m-d h:i:s');
        $solicitud->id_obra = Context::getIdObra();
    }

    public function created(SolicitudPagoAnticipado $solicitud)
    {
        $solicitud->generaTransaccionRubro();
    }
}