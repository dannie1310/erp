<?php

namespace App\Events;

use App\Models\REPSEG\FinFacIngresoFactura;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EnvioIngresoFactura
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $factura;
    public $archivo;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(FinFacIngresoFactura $factura, $archivo)
    {
        $this->factura = $factura;
        $this->archivo = $archivo;
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
