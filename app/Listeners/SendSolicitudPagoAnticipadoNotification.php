<?php

namespace App\Listeners;

use App\Events\AperturaInvitacion;
use App\Events\SolicitudAutorizacionPagoAnticipado;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Notifications\NotificacionInvitacionAbierta;
use App\Notifications\NotificacionSolicitudAutorizacionPagoAnticipado;
use Illuminate\Support\Facades\Notification;
use Twilio\Rest\Client;

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
        $usuarios_suscripcion = Usuario::suscripcion($suscripciones)->get();
        $permiso = ["autorizar_rechazar_solicitud_pago"];

        $usuarios_interesados_permisos = Usuario::usuarioPermisoGlobal(
            $permiso
        )->get();



        $usuarios_notificacion = $usuarios_suscripcion->diff($usuarios_interesados_permisos);
        Notification::send($usuarios_notificacion, new NotificacionSolicitudAutorizacionPagoAnticipado($event->solicitud));
        Notification::send(auth()->user(), new NotificacionSolicitudAutorizacionPagoAnticipado($event->solicitud));

    }
}
