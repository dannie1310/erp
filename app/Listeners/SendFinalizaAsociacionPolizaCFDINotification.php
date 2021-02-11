<?php

namespace App\Listeners;

use App\Events\FinalizaProcesamientoAsociacion;
use App\Events\FinalizaProcesamientoLoteBusquedas;
use App\Events\IncidenciaCI;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
use App\Notifications\NotificacionFinalizaProcesoAsociacionPolizasCFDI;
use App\Notifications\NotificacionFinalizaProcesoBusquedasDiferenciasPolizas;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\IGH\Usuario;
use Illuminate\Support\Facades\Notification;
use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use App\Notifications\NotificacionIncidenciasCI;

class SendFinalizaAsociacionPolizaCFDINotification
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
    public function handle(FinalizaProcesamientoAsociacion $event)
    {
        $suscripciones = Suscripcion::activa()->where("id_evento",$event->tipo)->get();
        $usuario = Usuario::suscripcion($suscripciones, $event->solicitud_asociacion->usuario_inicio)->get();
        Notification::send($usuario, new NotificacionFinalizaProcesoAsociacionPolizasCFDI($event->solicitud_asociacion));
    }
}
