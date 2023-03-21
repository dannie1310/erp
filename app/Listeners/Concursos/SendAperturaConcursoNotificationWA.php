<?php

namespace App\Listeners\Concursos;

use App\Events\Concursos\FinalizacionDeAperturaConcurso;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use Twilio\Rest\Client;

class SendAperturaConcursoNotificationWA
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
     * @param FinalizacionDeAperturaConcurso $event
     * @return void
     */
    public function handle(FinalizacionDeAperturaConcurso $event)
    {
        $suscripciones = Suscripcion::activa()->where("id_evento",$event->tipo)->get();
        $usuarios_suscripcion = Usuario::suscripcion($suscripciones)->get();
        $usuarios_interesados_por_registro = Usuario::whereIn("idusuario",[$event->concurso->id_usuario_inicio_apertura
        ,$event->concurso->id_usuario_finalizo_apertura])
        ->get();

        $usuarios_notificacion = $usuarios_suscripcion->merge($usuarios_interesados_por_registro);

        $ruta_api = "concursos/concurso-scope/".$event->concurso->id."/pdf";
        $mediaUrl = "https://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}/api/".$ruta_api."?access_token=";
        $body = "Se le informa que ha finalizado el proceso de presentación y apertura de ofertas del concurso *".$event->concurso->nombre."*.

Puede consultar el resultado visitando el sitio web de seguimiento.";
        foreach ($usuarios_notificacion as $usuario)
        {
            $recipient ="whatsapp:". $usuario->numero_celular;
            $twilio_whatsapp_number = config('app.env_variables.TWILIO_WHATSAPP_NUMBER');
            $account_sid = config('app.env_variables.TWILIO_SID');
            $auth_token = config('app.env_variables.TWILIO_AUTH_TOKEN');

            $tokenobj = $usuario->createToken('consultar-formato-apertura-concurso',['consultar-formato-apertura-concurso']);
            $token = $tokenobj->accessToken;

            $client = new Client($account_sid, $auth_token);
            $client->messages->create($recipient, array(
                'from' => "whatsapp:$twilio_whatsapp_number",
                'body' => $body
            ));
            $client->messages->create($recipient, array(
                'from' => "whatsapp:$twilio_whatsapp_number",
                'body' => "Informe Apertura de Concurso",
                'mediaUrl' => $mediaUrl.$token,
            ));
        }
    }
}
