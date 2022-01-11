<?php

namespace App\Listeners;


use App\Events\EnvioCotizacion;
use App\Events\EnvioPresupuesto;
use App\Events\RegistroInvitacion;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Notifications\NotificacionCotizacionEnviada;
use App\Notifications\NotificacionCredenciales;
use App\Models\IGH\Usuario;
use App\Notifications\NotificacionInvitacionCotizar;
use App\Notifications\NotificacionPresupuestoEnviado;
use Illuminate\Support\Facades\Notification;


class SendPresupuestoEnviadoNotification
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
    public function handle(EnvioPresupuesto $event)
    {


        if($event->invitacion->id_area_contratante == 1)
        {
            $suscripciones = Suscripcion::activa()->where("id_evento",$event->tipo)->get();
            $usuario = Usuario::suscripcion($suscripciones)->get();
            Notification::send($usuario, new NotificacionPresupuestoEnviado($event->invitacion, $event->cotizacion));
        }

        if($event->invitacion->copiados()->count()>0){
            Notification::route("mail",$event->invitacion->copiados()->pluck("direccion"))->notify(new NotificacionPresupuestoEnviado($event->invitacion));
        }

        Notification::send($event->invitacion->usuarioInvito, new NotificacionPresupuestoEnviado($event->invitacion, $event->cotizacion));
    }
}
