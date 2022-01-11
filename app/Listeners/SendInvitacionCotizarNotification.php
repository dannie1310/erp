<?php

namespace App\Listeners;


use App\Events\RegistroInvitacion;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
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
        if($event->invitacion->id_area_compradora == 1)
        {
            $suscripciones = Suscripcion::activa()->where("id_evento",11)->get();
            $usuario = Usuario::suscripcion($suscripciones)->get();
            Notification::send($usuario, new NotificacionInvitacionCotizarCopiados($event->invitacion));
        }
        else if($event->invitacion->id_area_contratante == 1)
        {
            $suscripciones = Suscripcion::activa()->where("id_evento",9)->get();
            $usuario = Usuario::suscripcion($suscripciones)->get();
            Notification::send($usuario, new NotificacionInvitacionCotizarCopiados($event->invitacion));
        }
        if($event->invitacion->copiados()->count()>0){
            Notification::route("mail",$event->invitacion->copiados()->pluck("direccion"))->notify(new NotificacionInvitacionCotizarCopiados($event->invitacion));
        }
        Notification::send($event->invitacion->usuarioInvitado, new NotificacionInvitacionCotizar($event->invitacion));
    }
}
