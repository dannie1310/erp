<?php

namespace App\Listeners;

use App\Events\AperturaInvitacion;
use App\Notifications\NotificacionInvitacionAbierta;
use Illuminate\Support\Facades\Notification;

class SendAperturaInvitacionNotification
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
     * @param AperturaInvitacion $event
     */
    public function handle(AperturaInvitacion $event)
    {
        Notification::send($event->invitacion->usuarioInvito, new NotificacionInvitacionAbierta($event->invitacion));
    }
}
