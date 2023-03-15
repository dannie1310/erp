<?php

namespace App\Listeners;

use App\Events\AperturaInvitacion;
use App\Events\FinalizacionDeAperturaConcurso;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Notifications\NotificacionAperturaConcurso;
use App\Notifications\NotificacionInvitacionAbierta;
use Illuminate\Support\Facades\Notification;
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
        $usuarios = Usuario::suscripcion($suscripciones)->get();
        $ruta_api = "concursos/concurso-scope/".$event->concurso->id."/pdf";
        $ruta_api_img = "concursos/concurso-scope/".$event->concurso->id."/grafica-png";
        $mediaUrl = "https://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}/api/".$ruta_api."?access_token=";
        $mediaUrlImg = "https://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}/api/".$ruta_api_img."?access_token=";
        $body = "Se le informa que la apertura del concurso ".$event->concurso->nombre." ha finalizado teniendo los siguientes resultados:"
            ."\nOferta Ganadora: ".$event->concurso->participanteGanador->nombre ." ".$event->concurso->participanteGanador->monto_format
            ."\nOferta Hermes: " .$event->concurso->participanteHermes->nombre ." ".$event->concurso->participanteHermes->monto_format
            ."\nLugar Obtenido: " .$event->concurso->participanteHermes->lugar." "
            ."\nPromedio de Ofertas: " . $event->concurso->promedio_format
            ."\nDiferencia Oferta Hermes vs Oferta Ganadora: " .$event->concurso->participanteHermes->distancia_primer_lugar_format." (".$event->concurso->participanteHermes->distancia_primer_lugar_porcentaje.")";

        foreach ($usuarios as $usuario)
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
            $client->messages->create($recipient, array(
                'from' => "whatsapp:$twilio_whatsapp_number",
                'body' => "Apertura de Concurso " . $event->concurso->nombre,
                'mediaUrl' => $mediaUrlImg.$token,
            ));
        }

        $recipient ="whatsapp:". $event->concurso->usuarioFinalizoApertura->numero_celular;
        $twilio_whatsapp_number = config('app.env_variables.TWILIO_WHATSAPP_NUMBER');
        $account_sid = config('app.env_variables.TWILIO_SID');
        $auth_token = config('app.env_variables.TWILIO_AUTH_TOKEN');

        $tokenobj = $event->concurso->usuarioFinalizoApertura->createToken('consultar-formato-apertura-concurso',['consultar-formato-apertura-concurso']);
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
        $client->messages->create($recipient, array(
            'from' => "whatsapp:$twilio_whatsapp_number",
            'body' => "Apertura de Concurso " . $event->concurso->nombre,
            'mediaUrl' => $mediaUrlImg.$token,
        ));

    }
}
