<?php

namespace App\Events;

use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCFDI;
use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\LoteBusqueda;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FinalizaProcesamientoAsociacion
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $solicitud_asociacion;
    public $tipo;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SolicitudAsociacionCFDI $solicitud_asociacion)
    {
        $this->solicitud_asociacion = $solicitud_asociacion;
        $this->tipo = 5;
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
