<?php

namespace App\Listeners;


use App\Events\CambioFechaCierreInvitacion;
use App\Events\RegistroInvitacion;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Notifications\NotificacionCambioFechaCierreInvitacion;
use App\Notifications\NotificacionCredenciales;
use App\Models\IGH\Usuario;
use App\Notifications\NotificacionInvitacionCotizar;
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
        $suscripciones = Suscripcion::activa()->where("id_evento",$event->tipo)->get();
        //$usuario = Usuario::suscripcion($suscripciones)->get();

        //Notification::send($usuario, new NotificacionInvitacionCotizar($event->invitacion));
        Notification::send($event->invitacion->usuarioInvitado, new NotificacionCambioFechaCierreInvitacion($event->invitacion));
    }
}
