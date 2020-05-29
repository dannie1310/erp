<?php

namespace App\Events;

use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\LoteBusqueda;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FinalizaProcesamientoLoteBusquedas
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $lote;
    public $tipo;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(LoteBusqueda $lote)
    {
        $this->lote = $lote;
        $this->tipo = 1;
        //
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
