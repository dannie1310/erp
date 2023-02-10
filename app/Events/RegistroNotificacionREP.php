<?php

namespace App\Events;

use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;

class RegistroNotificacionREP
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $proveedor;
    public $tipo;
    public $destinatarios;

    public function __construct(ProveedorSAT $proveedor, $destinatarios)
    {
        $this->proveedor = $proveedor;
        $this->destinatarios = $destinatarios;
        $this->tipo = 18;
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
