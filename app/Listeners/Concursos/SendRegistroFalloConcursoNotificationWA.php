<?php

namespace App\Listeners\Concursos;

use App\Events\Concursos\FinalizacionDeAperturaConcurso;
use App\Events\Concursos\InicioDeAperturaConcurso;
use App\Events\Concursos\RegistroFalloConcurso;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use DateTime;
use DateTimeZone;
use Twilio\Rest\Client;

class SendRegistroFalloConcursoNotificationWA
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
    public function handle(RegistroFalloConcurso $event)
    {
        $fecha_registro_apertura = new DateTime($event->concurso->fecha_hora_inicio_apertura);
        $fecha_registro_apertura->setTimezone(new DateTimeZone('America/Mexico_City'));
        $fecha_apertura = new DateTime($event->concurso->fecha);
        $fecha_apertura->setTimezone(new DateTimeZone('America/Mexico_City'));
        $diferencia_dias = $fecha_registro_apertura->diff($fecha_apertura)->days;

        if($diferencia_dias == 0) {
            $suscripciones = Suscripcion::activa()->where("id_evento", $event->tipo)->get();
            $usuarios_notificacion = Usuario::suscripcion($suscripciones)->get();

            $body = "Se le informa que se ha registrado el fallo del concurso *" . $event->concurso->nombre . "* mismo que se llevó a cabo el {{2}}, quedando como ganador:

 *" . $event->concurso->participanteGanador . "*.

Puede consultar mas detalles del concurso visitando el sitio web de seguimiento.";
            foreach ($usuarios_notificacion as $usuario) {
                $recipient = "whatsapp:" . $usuario->numero_celular;
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
}
