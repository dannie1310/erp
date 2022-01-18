<?php

namespace App\Listeners;

use App\Events\AperturaInvitacion;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
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
        if($event->invitacion->id_area_compradora == 1)
        {
            $suscripciones = Suscripcion::activa()->where("id_evento",13)->get();
            $usuario = Usuario::suscripcion($suscripciones)->get();
            Notification::send($usuario, new NotificacionInvitacionAbierta($event->invitacion));
        }
        else if($event->invitacion->id_area_contratante == 1)
        {
            $suscripciones = Suscripcion::activa()->where("id_evento",14)->get();
            $usuario = Usuario::suscripcion($suscripciones)->get();
            Notification::send($usuario, new NotificacionInvitacionAbierta($event->invitacion));
        }

        if($event->invitacion->copiados()->count()>0){
            Notification::route("mail",$event->invitacion->copiados()->pluck("direccion"))->notify(new NotificacionInvitacionAbierta($event->invitacion));
        }
        Notification::send($event->invitacion->usuarioInvito, new NotificacionInvitacionAbierta($event->invitacion));
    }
}
