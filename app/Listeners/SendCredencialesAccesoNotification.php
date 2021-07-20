<?php

namespace App\Listeners;

use App\Events\FinalizaCargaCFD;
use App\Events\FinalizaProcesamientoLoteBusquedas;
use App\Events\IncidenciaCI;
use App\Events\RegistroSolicitudRecepcionCFDI;
use App\Events\RegistroUsuarioProveedor;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Models\SEGURIDAD_ERP\Obra;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
use App\Notifications\NotificacionCredenciales;
use App\Notifications\NotificacionFinalizaCargaCFD;
use App\Notifications\NotificacionFinalizaProcesoBusquedasDiferenciasPolizas;
use App\Notifications\NotificacionSolicitudRecepcionCFDI;
use App\Notifications\NotificacionSolicitudRecepcionCFDIProveedor;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\IGH\Usuario;
use Illuminate\Support\Facades\Notification;
use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use App\Notifications\NotificacionIncidenciasCI;

class SendCredencialesAccesoNotification
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
     * @param RegistroSolicitudRecepcionCFDI $event
     */
    public function handle($event)
    {
        $suscripciones = Suscripcion::activa()->where("id_evento",$event->tipo)->get();

        $usuario = Usuario::suscripcion($suscripciones)->get();

        //Notification::send($usuario, new NotificacionCredenciales($event->solicitud));
        Notification::send($event->usuario, new NotificacionCredenciales($event->usuario, $event->clave));

    }
}
