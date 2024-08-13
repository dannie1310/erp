<?php

namespace App\Events\IFS;

use App\Models\CTPQ\NmNominas\Nom10015;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EnvioXMLPolizaNominas
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $poliza;
    public $destinatarios;
    public $xml;
    public $archivo;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Nom10015 $poliza,$destinatarios, $xml, $archivo)
    {
        $this->poliza= $poliza;
        $this->destinatarios = $destinatarios;
        $this->xml = $xml;
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
