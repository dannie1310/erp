<?php

namespace App\Listeners;


use App\Events\RegistroInvitacion;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Notifications\NotificacionCredenciales;
use App\Models\IGH\Usuario;
use App\Notifications\NotificacionInvitacionCotizar;
use Illuminate\Support\Facades\Notification;


class SendInvitacionCotizarNotification
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
     * @param RegistroInvitacion $event
     */
    public function handle(RegistroInvitacion $event)
    {
        $suscripciones = Suscripcion::activa()->where("id_evento",$event->tipo)->get();
        //$usuario = Usuario::suscripcion($suscripciones)->get();

        //Notification::send($usuario, new NotificacionInvitacionCotizar($event->invitacion));
        Notification::send($event->invitacion->usuarioInvitado, new NotificacionInvitacionCotizar($event->invitacion));
    }
}
