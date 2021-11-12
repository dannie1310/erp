<?php

namespace App\Events;

use App\Models\CADECO\CotizacionCompra;
use App\Models\CADECO\PresupuestoContratista;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;

class EnvioPresupuesto
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $invitacion;
    public $cotizacion;
    public $tipo;

    public function __construct(Invitacion $invitacion, PresupuestoContratista $cotizacion)
    {
        $this->invitacion = $invitacion;
        $this->cotizacion = $cotizacion;
        $this->tipo = 10;
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
