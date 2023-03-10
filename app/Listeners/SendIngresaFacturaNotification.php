<?php

namespace App\Listeners;

use App\Events\EnvioIngresoFactura;
use App\Models\REPSEG\GrlNotificacion;
use App\Notifications\NotificacionIngresoFacturaEnviada;
use Illuminate\Support\Facades\Mail;

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
        $destinatarios = $event->factura->getToNotificacionIngreso();
        $destinatarios_copiados = $event->factura->getCCNotificacionIngreso();
        $destinatarios_ocultos = $event->factura->getCCONotificacionIngreso();
        Mail::to($destinatarios)->cc($destinatarios_copiados)->bcc($destinatarios_ocultos)->queue(new NotificacionIngresoFacturaEnviada($event->factura, $event->archivo, $event->xml));
    }
}
