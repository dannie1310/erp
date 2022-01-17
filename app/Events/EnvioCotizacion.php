<?php

namespace App\Events;

use App\Models\CADECO\CotizacionCompra;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;

class EnvioCotizacion
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $invitacion;
    public $cotizacion;
    public $tipo;

    public function __construct(Invitacion $invitacion, CotizacionCompra $cotizacion)
    {
        $this->invitacion = $invitacion;
        $this->cotizacion = $cotizacion;
        $this->tipo = 12;
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
