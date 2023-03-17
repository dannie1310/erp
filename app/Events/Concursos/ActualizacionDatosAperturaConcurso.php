<?php

namespace App\Events\Concursos;

use App\Models\SEGURIDAD_ERP\Concursos\Concurso;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActualizacionDatosAperturaConcurso
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
        $this->tipo = 21;
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
