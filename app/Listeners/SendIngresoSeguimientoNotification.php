<?php

namespace App\Listeners;

use App\Events\EnvioIngresoFactura;
use App\Events\EnvioIngresoSeguimiento;
use App\Mail\NotificacionIngresoFacturaEnviada;
use Illuminate\Support\Facades\Mail;

class SendIngresoSeguimientoNotification
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
     * @param EnvioIngresoSeguimiento $event
     * @return void
     */
    public function handle(EnvioIngresoSeguimiento $event)
    {dd($event);
        $destinatarios = $event->factura->getToNotificacionIngreso();
        $destinatarios_copiados = $event->factura->getCCNotificacionIngreso();
        $destinatarios_ocultos = $event->factura->getCCONotificacionIngreso();
        Mail::to($destinatarios)->cc($destinatarios_copiados)->bcc($destinatarios_ocultos)->sendNow(new NotificacionIngresoFacturaEnviada($event->factura, $event->archivo, $event->xml));
    }
}
