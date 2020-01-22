<?php

namespace App\Listeners;

use App\Events\IncidenciaCI;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\IGH\Usuario;
use Illuminate\Support\Facades\Notification;
use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use App\Notifications\NotificacionIncidenciasCI;

class SendIncidenciaCINotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  IncidenciaCI  $event
     * @return void
     */
    public function handle(IncidenciaCI $event)
    {
        $incidencia = Incidencia::create($event->data);
        if($incidencia->tipo->notificable == 1)
        {
            $usuario = Usuario::notificacionCI()->get();
            Notification::send($usuario, new NotificacionIncidenciasCI($incidencia, $event->data));
        }
    }
}
