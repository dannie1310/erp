<?php

namespace App\Events;

use App\Models\CADECO\SolicitudPagoAnticipado;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;

class SolicitudAutorizacionPagoAnticipado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $solicitud;
    public $tipo;

    public function __construct(SolicitudPagoAnticipado $solicitud)
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
