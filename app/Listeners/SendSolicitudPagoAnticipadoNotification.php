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

        $usuarios_interesados_permisos = usuario::usuarioPermisoGlobal(
            $permiso
            , $event->solicitud->id_proyecto_obra)->get();

        $usuarios_notificacion = $usuarios_suscripcion->diff($usuarios_interesados_permisos);

        Notification::send($usuarios_notificacion, new NotificacionSolicitudAutorizacionPagoAnticipado($event->solicitud));
        Notification::send($event->solicitud->usuario, new NotificacionSolicitudAutorizacionPagoAnticipado($event->solicitud));

        foreach($usuarios_interesados_permisos as $usuario_interesado_permiso)
        {
            //if($usuario_interesado_permiso->numero_celular)
            //{
                $tokenobj = $usuario_interesado_permiso->createToken('autorizar-solicitud-pago-anticipado',['autorizar-solicitudes-pago-anticipado']);
                $token = $tokenobj->accessToken;
                $token_id = $tokenobj->token->id;

                Notification::send($event->solicitud->usuario, new NotificacionSolicitudAutorizacionPagoAnticipado($event->solicitud, $token));

            //}
        }
    }
}
