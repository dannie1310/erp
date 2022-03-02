<?php

namespace App\Listeners;

use App\Events\AperturaInvitacion;
use App\Events\SolicitudAutorizacionPagoAnticipado;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Notifications\NotificacionInvitacionAbierta;
use App\Notifications\NotificacionSolicitudAutorizacionPagoAnticipado;
use Illuminate\Support\Facades\Notification;

class SendSolicitudPagoAnticipadoNotification
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
    public function handle(SolicitudAutorizacionPagoAnticipado $event)
    {
        $suscripciones = Suscripcion::activa()->where("id_evento",$event->tipo)->get();
        $usuario = Usuario::suscripcion($suscripciones)->get();
        Notification::send($usuario, new NotificacionSolicitudAutorizacionPagoAnticipado($event->solicitud));
        Notification::send($event->solicitud->usuario, new NotificacionSolicitudAutorizacionPagoAnticipado($event->solicitud));
    }
}
