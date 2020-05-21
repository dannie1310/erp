<?php

namespace App\Listeners;

use App\Events\FinalizaProcesamientoLoteBusquedas;
use App\Events\IncidenciaCI;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
use App\Notifications\NotificacionFinalizaProcesoBusquedasDiferenciasPolizas;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\IGH\Usuario;
use Illuminate\Support\Facades\Notification;
use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use App\Notifications\NotificacionIncidenciasCI;

class SendFinalizaProcesamientoLoteBusquedaNotification
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
    public function handle(FinalizaProcesamientoLoteBusquedas $event)
    {
        //$usuario = Usuario::notificacionCI()->get();
        $suscripciones = Suscripcion::activa()->where("id_evento",$event->tipo)->get();
        $usuario = Usuario::suscripcion($suscripciones)->get();
        $diferencias_totales = Diferencia::totalPorTipoPorEmpresa();
        Notification::send($usuario, new NotificacionFinalizaProcesoBusquedasDiferenciasPolizas($event->lote, $diferencias_totales));
    }
}
