<?php

namespace App\Listeners;

use App\Events\AperturaInvitacion;
use App\Events\FinalizacionDeAperturaConcurso;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Notifications\NotificacionAperturaConcurso;
use App\Notifications\NotificacionInvitacionAbierta;
use Illuminate\Support\Facades\Notification;

class SendAperturaConcursoNotification
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
        $usuario = Usuario::suscripcion($suscripciones)->get();
        $tokenobj = $event->concurso->usuarioFinalizoApertura
            ->createToken('consultar-formato-apertura-concurso',['consultar-formato-apertura-concurso']);
        $token = $tokenobj->accessToken;
        Notification::send($usuario, new NotificacionAperturaConcurso($event->concurso, $token));
        Notification::send($event->concurso->usuarioFinalizoApertura, new NotificacionAperturaConcurso($event->concurso, $token));
    }
}
