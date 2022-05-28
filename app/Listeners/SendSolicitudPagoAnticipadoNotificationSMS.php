<?php

namespace App\Listeners;

use App\Events\AperturaInvitacion;
use App\Events\SolicitudAutorizacionPagoAnticipado;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\EsquemaAutorizacion\Token;
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

                    Token::create([
                        "id_firmante"=>$usuario_interesado_permiso->firmante->id,
                        "id_transaccion"=>$event->solicitud->transaccionGeneral->id,
                        "token"=>$token
                    ]);

                    /**
                     * WHATSAPP
                     */

                    //$recipient = "whatsapp:+525546347020" ;
                    //$recipient = "whatsapp:+5215518673524" ;
                    //$recipient = "whatsapp:+5215541353541";

                    $recipient ="whatsapp:". $usuario_interesado_permiso->numero_celular;
                    $twilio_whatsapp_number = config('app.env_variables.TWILIO_WHATSAPP_NUMBER');
                    $account_sid = config('app.env_variables.TWILIO_SID');
                    $auth_token = config('app.env_variables.TWILIO_AUTH_TOKEN');

                    $client = new Client($account_sid, $auth_token);
                    $message = $client->messages->create($recipient, array(
                        'from' => "whatsapp:$twilio_whatsapp_number",
                        'body' => "Se le informa que ".$event->solicitud->transaccionGeneral->usuarioRegistro->nombre_completo." ha solicitado que se autorice el pago anticipado que se describe a continuación:"
                            ."\nIdentificador: " .$event->solicitud->transaccionGeneral->id." "
                            ."\nProyecto: " .$event->solicitud->obra->nombre." "
                            ."\nFolio: " .$event->solicitud->numero_folio_format." "
                            ."\nProveedor: ".$event->solicitud->empresa->razon_social." "
                            ."\nMonto: ".$event->solicitud->monto_format." "
                            ."\nMotivo: ".$event->solicitud->observaciones." "
                            ."\nAutorizar: AUT ".$event->solicitud->transaccionGeneral->id." "
                            ."\nRechazar: REC ".$event->solicitud->transaccionGeneral->id." "

                    ));
                    //print_r($message);

                    /*
                    ."\n \n Autorizar: https://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT']."/api/solicitud-pago-anticipado/".$event->solicitud->transaccionGeneral->id."/autorizar?access_token=".$token." "
                    */


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
