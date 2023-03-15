<?php

namespace App\Events;

use App\Models\SEGURIDAD_ERP\Concursos\Concurso;
use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FinalizacionDeAperturaConcurso
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $concurso;
    public $tipo;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Concurso $concurso)
    {
        $this->concurso = $concurso;
        $this->tipo = 19;
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
