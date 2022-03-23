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

class SendSolicitudPagoAnticipadoNotificationParaAutorizacion
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
        $nivel_bajo_pendiente = $event->solicitud->autorizacionesRequeridas()
            ->whereNull("id_firmante")
            ->orderBy("nivel_requerido","desc")->pluck("nivel_requerido")->first();

        $permiso = ["autorizar_rechazar_solicitud_pago"];

        $usuarios_interesados_permisos = Usuario::usuarioPermisoGlobal(
            $permiso
        )->get();

        foreach($usuarios_interesados_permisos as $usuario_interesado_permiso)
        {
            if($usuario_interesado_permiso->firmante && $usuario_interesado_permiso->firmante->nivelAutorizacion && $usuario_interesado_permiso->firmante->nivelAutorizacion->nivel == $nivel_bajo_pendiente)
            {
                $tokenobj = $usuario_interesado_permiso->createToken('autorizar-solicitud-pago-anticipado',['autorizar-solicitudes-pago-anticipado']);
                $token = $tokenobj->accessToken;
                $token_id = $tokenobj->token->id;
                Notification::send($usuario_interesado_permiso, new NotificacionSolicitudAutorizacionPagoAnticipado($event->solicitud, $token, $usuario_interesado_permiso->nombre_completo));
            }
        }
    }
}
