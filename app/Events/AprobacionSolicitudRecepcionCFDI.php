<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/07/2020
 * Time: 11:36 PM
 */

namespace App\Events;

use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDI;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;

class AprobacionSolicitudRecepcionCFDI
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $solicitud;
    public $tipo;

    public function __construct(SolicitudRecepcionCFDI $solicitud)
    {
        $this->solicitud = $solicitud;
        $this->tipo = 7;
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
