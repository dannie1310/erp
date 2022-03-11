<?php

namespace App\Listeners;

use App\Events\AutorizacionPagoAnticipado;
use App\Events\SolicitudAutorizacionPagoAnticipado;
use App\Notifications\NotificacionAutorizacionPagoAnticipado;
use Illuminate\Support\Facades\Notification;

class SendAutorizacionPagoAnticipadoNotification
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
    public function handle(AutorizacionPagoAnticipado $event)
    {
        Notification::send($event->solicitud_pago_autorizacion->usuarioRegistro, new NotificacionAutorizacionPagoAnticipado($event->solicitud_pago_autorizacion, $event->solicitud_pago_anticipado));
    }
}
