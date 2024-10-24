<?php

namespace App\Events;

use App\Models\REPSEG\VwFinIngresoRegistrado;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EnvioIngresoSeguimiento
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ingreso;
    public $archivo;
    public $xml;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(VwFinIngresoRegistrado $ingreso)
    {
        $this->ingreso = $ingreso;
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
