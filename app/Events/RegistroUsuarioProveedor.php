<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/07/2020
 * Time: 11:36 PM
 */

namespace App\Events;

use App\Models\IGH\Usuario;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;

class RegistroUsuarioProveedor
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $usuario;
    public $tipo;
    public $clave;
    public $reset;

    public function __construct(Usuario $usuario, $clave, $reset = null)
    {
        $this->usuario = $usuario;
        $this->clave = $clave;
        $this->tipo = 8;
        $this->reset = $reset;
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
