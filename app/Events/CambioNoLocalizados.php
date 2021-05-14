<?php


namespace App\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CambioNoLocalizados
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $cambios;
    public $tipo;

    public function __construct($cambios)
    {
        $this->cambios = $cambios;
        $this->tipo = 3;
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
