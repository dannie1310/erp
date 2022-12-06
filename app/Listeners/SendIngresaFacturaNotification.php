<?php

namespace App\Listeners;

use App\Events\EnvioIngresoFactura;
use App\Models\IGH\Usuario;
use App\Notifications\NotificacionIngresoFacturaEnviada;
use Illuminate\Support\Facades\Notification;

class SendIngresaFacturaNotification
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
     * @param EnvioIngresoFactura $event
     * @return void
     */
    public function handle(EnvioIngresoFactura $event)
    {
       //$suscripciones = Suscripcion::activa()->where("id_evento",$event->tipo)->get();
            $usuario = Usuario::where('idusuario', 3250)->select('correo')->get();
        //dd(implode(';', $event->factura->getToNotificacionIngreso()), $usuario,Array($event->factura->getToNotificacionIngreso());
            Notification::send($usuario, new NotificacionIngresoFacturaEnviada($event->factura, $event->archivo));
           // Notification::route("mail",$event->invitacion->copiados()->pluck("direccion"))->notify(new NotificacionCotizacionEnviada($event->invitacion));


       // Notification::send($event->invitacion->usuarioInvito, new NotificacionCotizacionEnviada($event->invitacion, $event->cotizacion));
    }
}
