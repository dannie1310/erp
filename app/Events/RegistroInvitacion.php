<?php

namespace App\Events;

use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;

class RegistroInvitacion
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $invitacion;
    public $tipo;

    public function __construct(Invitacion $invitacion)
    {
        $this->invitacion = $invitacion;
        $this->tipo = 9;
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
