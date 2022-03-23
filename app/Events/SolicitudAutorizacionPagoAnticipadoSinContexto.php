<?php

namespace App\Events;

use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacion;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;

class SolicitudAutorizacionPagoAnticipadoSinContexto
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $solicitud;
    public $tipo;

    public function __construct(SolicitudPagoAutorizacion $solicitud)
    {
        $this->solicitud = $solicitud;
        $this->tipo = 17;
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
