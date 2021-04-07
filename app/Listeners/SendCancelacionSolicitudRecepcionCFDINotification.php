<?php

namespace App\Listeners;

use App\Events\AprobacionSolicitudRecepcionCFDI;
use App\Events\CancelacionSolicitudRecepcionCFDI;
use App\Events\FinalizaCargaCFD;
use App\Events\FinalizaProcesamientoLoteBusquedas;
use App\Events\IncidenciaCI;
use App\Events\RechazoSolicitudRecepcionCFDI;
use App\Events\RegistroSolicitudRecepcionCFDI;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Models\SEGURIDAD_ERP\Obra;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
use App\Notifications\NotificacionAprobacionSolicitudRecepcionCFDI;
use App\Notifications\NotificacionAprobacionSolicitudRecepcionCFDIProveedor;
use App\Notifications\NotificacionCancelacionSolicitudRecepcionCFDI;
use App\Notifications\NotificacionCancelacionSolicitudRecepcionCFDIProveedor;
use App\Notifications\NotificacionFinalizaCargaCFD;
use App\Notifications\NotificacionFinalizaProcesoBusquedasDiferenciasPolizas;
use App\Notifications\NotificacionRechazoSolicitudRecepcionCFDI;
use App\Notifications\NotificacionRechazoSolicitudRecepcionCFDIProveedor;
use App\Notifications\NotificacionSolicitudRecepcionCFDI;
use App\Notifications\NotificacionSolicitudRecepcionCFDIProveedor;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\IGH\Usuario;
use Illuminate\Support\Facades\Notification;
use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use App\Notifications\NotificacionIncidenciasCI;

class SendCancelacionSolicitudRecepcionCFDINotification
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
     * @param RechazoSolicitudRecepcionCFDI $event
     */
    public function handle(CancelacionSolicitudRecepcionCFDI $event)
    {
        $suscripciones = Suscripcion::activa()->where("id_evento",$event->tipo)->get();
        $roles = ["contador","administrador_de_obra","comprador_compras","comprador_contratos","comprador_maquinaria","cuentas_por_pagar"];

        $usuarios_interesados_permisos = usuario::usuarioRol(
            $roles
            , $event->solicitud->id_proyecto_obra)->get();
        $usuario = Usuario::suscripcion($suscripciones)->get();

        Notification::send($usuario, new NotificacionCancelacionSolicitudRecepcionCFDI($event->solicitud));
        Notification::send($usuarios_interesados_permisos, new NotificacionCancelacionSolicitudRecepcionCFDI($event->solicitud));
        Notification::send($event->solicitud->usuarioRegistro, new NotificacionCancelacionSolicitudRecepcionCFDIProveedor($event->solicitud));
        if($event->solicitud->usuarioRegistro->correo != $event->solicitud->correo_notificaciones){
            Notification::route("mail",$event->solicitud->correo_notificaciones)->notify(new NotificacionCancelacionSolicitudRecepcionCFDIProveedor($event->solicitud));
        }
    }
}
