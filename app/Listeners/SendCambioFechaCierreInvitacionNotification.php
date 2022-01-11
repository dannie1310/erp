<?php

namespace App\Listeners;


use App\Events\CambioFechaCierreInvitacion;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Notifications\NotificacionCambioFechaCierreInvitacion;
use App\Models\IGH\Usuario;
use Illuminate\Support\Facades\Notification;


class SendCambioFechaCierreInvitacionNotification
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
     * @param CambioFechaCierreInvitacion $event
     */
    public function handle(CambioFechaCierreInvitacion $event)
    {
        if($event->invitacion->id_area_compradora == 1)
        {
            $suscripciones = Suscripcion::activa()->where("id_evento",15)->get();
            $usuario = Usuario::suscripcion($suscripciones)->get();
            Notification::send($usuario, new NotificacionCambioFechaCierreInvitacion($event->invitacion));
        }
        else if($event->invitacion->id_area_contratante == 1)
        {
            $suscripciones = Suscripcion::activa()->where("id_evento",16)->get();
            $usuario = Usuario::suscripcion($suscripciones)->get();
            Notification::send($usuario, new NotificacionCambioFechaCierreInvitacion($event->invitacion));
        }

        if($event->invitacion->copiados()->count()>0){
            Notification::route("mail",$event->invitacion->copiados()->pluck("direccion"))->notify(new NotificacionCambioFechaCierreInvitacion($event->invitacion));
        }
        Notification::send($event->invitacion->usuarioInvitado, new NotificacionCambioFechaCierreInvitacion($event->invitacion));
    }
}
