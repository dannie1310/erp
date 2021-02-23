<?php

namespace App\Listeners;

use App\Events\FinalizaCargaCFD;
use App\Events\FinalizaProcesamientoLoteBusquedas;
use App\Events\IncidenciaCI;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
use App\Notifications\NotificacionFinalizaCargaCFD;
use App\Notifications\NotificacionFinalizaProcesoBusquedasDiferenciasPolizas;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\IGH\Usuario;
use Illuminate\Support\Facades\Notification;
use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use App\Notifications\NotificacionIncidenciasCI;

class SendFinalizaCargaCFDNotification
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
     * @param FinalizaCargaCFD $event
     */
    public function handle(FinalizaCargaCFD $event)
    {
        $suscripciones = Suscripcion::activa()->where("id_evento",$event->tipo)->get();
        $usuario = Usuario::suscripcion($suscripciones, $event->carga->usuario_cargo)->get();

        Notification::send($usuario, new NotificacionFinalizaCargaCFD($event->carga));
    }
}
