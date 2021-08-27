<?php

namespace App\Listeners;


use App\Events\EnvioCotizacion;
use App\Events\RegistroInvitacion;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Notifications\NotificacionCotizacionEnviada;
use App\Notifications\NotificacionCredenciales;
use App\Models\IGH\Usuario;
use App\Notifications\NotificacionInvitacionCotizar;
use Illuminate\Support\Facades\Notification;


class SendCotizacionEnviadaNotification
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
     * @param EnvioCotizacion $event
     */
    public function handle(EnvioCotizacion $event)
    {
        $suscripciones = Suscripcion::activa()->where("id_evento",$event->tipo)->get();
        $usuario = Usuario::suscripcion($suscripciones)->get();

        Notification::send($usuario, new NotificacionCotizacionEnviada($event->invitacion, $event->cotizacion));
        Notification::send($event->invitacion->usuarioInvito, new NotificacionCotizacionEnviada($event->invitacion, $event->cotizacion));
    }
}
