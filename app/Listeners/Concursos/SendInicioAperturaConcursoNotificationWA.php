<?php

namespace App\Listeners\Concursos;

use App\Events\Concursos\FinalizacionDeAperturaConcurso;
use App\Events\Concursos\InicioDeAperturaConcurso;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use Twilio\Rest\Client;

class SendInicioAperturaConcursoNotificationWA
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
    public function handle(InicioDeAperturaConcurso $event)
    {
        $suscripciones = Suscripcion::activa()->where("id_evento",$event->tipo)->get();
        $usuarios_notificacion = Usuario::suscripcion($suscripciones)->get();

        $body = "Se le informa que se ha iniciado el proceso de apertura de ofertas del concurso ".$event->concurso->nombre.", ¿Desea recibir las notificaciones relacionadas con este proceso?";
        $body = "Se le informa que se ha iniciado el proceso de presentación y apertura de ofertas del concurso *".$event->concurso->nombre."*.

Puede realizar el seguimiento visitando el sitio web o teclear *SI* para recibir las notificaciones relacionadas.";
        foreach ($usuarios_notificacion as $usuario)
        {
            $recipient ="whatsapp:". $usuario->numero_celular;
            $twilio_whatsapp_number = config('app.env_variables.TWILIO_WHATSAPP_NUMBER');
            $account_sid = config('app.env_variables.TWILIO_SID');
            $auth_token = config('app.env_variables.TWILIO_AUTH_TOKEN');


            $client = new Client($account_sid, $auth_token);
            $client->messages->create($recipient, array(
                'from' => "whatsapp:$twilio_whatsapp_number",
                'body' => $body
            ));
        }
    }
}
