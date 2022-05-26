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

class SendSolicitudPagoAnticipadoNotificationSMS
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

        $usuarios_interesados_permisos = usuario::usuarioPermisoGlobal(
            $permiso
            , $event->solicitud->id_proyecto_obra)->get();

        foreach($usuarios_interesados_permisos as $usuario_interesado_permiso)
        {
            if($usuario_interesado_permiso->firmante && $usuario_interesado_permiso->firmante->nivelAutorizacion && $usuario_interesado_permiso->firmante->nivelAutorizacion->nivel == $nivel_bajo_pendiente) {
                if ($usuario_interesado_permiso->numero_celular) {
                    $tokenobj = $usuario_interesado_permiso->createToken('autorizar-solicitud-pago-anticipado', ['autorizar-solicitudes-pago-anticipado']);
                    $token = $tokenobj->accessToken;
                    $token_id = $tokenobj->token->id;

                    /**
                     * WHATSAPP
                     */

                    $recipient = "whatsapp:+5215546347020" /*. $usuario_interesado_permiso->numero_celular*/;
                    $twilio_whatsapp_number = config('app.env_variables.TWILIO_WHATSAPP_NUMBER');
                    $account_sid = config('app.env_variables.TWILIO_SID');
                    $auth_token = config('app.env_variables.TWILIO_AUTH_TOKEN');

                    $client = new Client($account_sid, $auth_token);
                    /*$message = $client->messages->create($recipient, array(
                        'from' => "whatsapp:$twilio_whatsapp_number",
                        'body' => "Se le informa que ".$event->solicitud->transaccionGeneral->usuarioRegistro->nombre_completo." ha solicitado que se autorice el pago anticipado que se describe a continuación:"
                            ."\nProyecto: " .$event->solicitud->obra->nombre." "
                            ."\nFolio: " .$event->solicitud->numero_folio_format." "
                            ."\nProveedor: ".$event->solicitud->empresa->razon_social." "
                            ."\nMonto: ".$event->solicitud->monto_format." "
                            ."\nMotivo: ".$event->solicitud->observaciones." "
                    ));*/

                    $message = $client->messages->create($recipient, array(
                        "from" => "whatsapp:+15076072235", /*"whatsapp:$twilio_whatsapp_number",*/
                        "body" => "prueba"
                    ));

                    print_r($twilio_whatsapp_number);
                    print_r($message);

                    /**
                     * SMS
                     */

                    $account_sid = config('app.env_variables.TWILIO_SID');
                    $auth_token = config('app.env_variables.TWILIO_AUTH_TOKEN');
                    $client = new Client($account_sid, $auth_token);
                    $recipient = $usuario_interesado_permiso->numero_celular;
                    $twilio_number = config('app.env_variables.TWILIO_SMS_NUMBER');
                    $client->messages
                        ->create($recipient, // to
                            [
                                "body" => mb_substr("" . $event->solicitud->transaccionGeneral->usuarioRegistro->usuario . " ha solicitado que se autorice el pago anticipado:"
                                    . "\nProyecto: " . $event->solicitud->obra->nombre . " "
                                    . "\nFolio: " . $event->solicitud->numero_folio_format . " "
                                    . "\nProveedor: " . $event->solicitud->empresa->razon_social . " "
                                    . "\nMonto: " . $event->solicitud->monto_format . " ", 0, 155)
                                ,
                                "from" => $twilio_number
                            ]
                        );

                }
            }
        }
    }
}
