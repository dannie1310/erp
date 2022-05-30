<?php

namespace App\Listeners;

use App\Events\RechazoPagoAnticipado;
use App\Events\SolicitudAutorizacionPagoAnticipado;
use App\Notifications\NotificacionRechazoPagoAnticipado;
use Illuminate\Support\Facades\Notification;

class SendRechazoPagoAnticipadoNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param SolicitudAutorizacionPagoAnticipado $event
     * @return void
     */
    public function handle(RechazoPagoAnticipado $event)
    {
        Notification::send($event->solicitud_pago_autorizacion->usuarioRegistro, new NotificacionRechazoPagoAnticipado($event->solicitud_pago_autorizacion, $event->solicitud_pago_anticipado, $event->motivo));
    }
}
