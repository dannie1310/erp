<?php

namespace App\Events\IFS;

use App\Models\CONTROL_RECURSOS\Documento;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EnvioXMLDocumentoRecursos
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $documento;
    public $destinatarios;
    public $xml;
    public $archivo;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Documento $documento,$destinatarios, $xml, $archivo)
    {
        $this->documento= $documento;
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
