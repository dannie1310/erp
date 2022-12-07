<?php

namespace App\Listeners;

use App\Events\EnvioIngresoFactura;
use App\Models\REPSEG\GrlNotificacion;
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
        $notificaciones = GrlNotificacion::activo()->seccion(1)->proyecto($event->factura->idproyecto)->where('tipo', 'TO')->select('cuenta')->get();
        Notification::send($notificaciones, new NotificacionIngresoFacturaEnviada($event->factura, $event->archivo, $event->xml));
    }
}
