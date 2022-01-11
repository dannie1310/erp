<?php

namespace App\Listeners;


use App\Events\RegistroInvitacion;
use App\Notifications\NotificacionInvitacionCotizar;
use App\Notifications\NotificacionInvitacionCotizarCopiados;
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
        if($event->invitacion->copiados()->count()>0){
            Notification::route("mail",$event->invitacion->copiados()->pluck("direccion"))->notify(new NotificacionInvitacionCotizarCopiados($event->invitacion));
        }
        Notification::send($event->invitacion->usuarioInvitado, new NotificacionInvitacionCotizar($event->invitacion));
    }
}
