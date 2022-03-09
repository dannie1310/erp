<?php

namespace App\Events;

use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacion;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;

class RechazoPagoAnticipado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $solicitud_pago_autorizacion;
    public $solicitud_pago_anticipado;

    public function __construct(SolicitudPagoAutorizacion $solicitud_pago_autorizacion, SolicitudPagoAnticipado $solicitud_pago_anticipado)
    {
        $this->solicitud_pago_autorizacion = $solicitud_pago_autorizacion;
        $this->solicitud_pago_anticipado = $solicitud_pago_anticipado;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

}
