<?php

namespace App\Listeners;

use App\Events\FinalizaCargaCFD;
use App\Events\FinalizaProcesamientoLoteBusquedas;
use App\Events\IncidenciaCI;
use App\Events\RegistroSolicitudRecepcionCFDI;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Models\SEGURIDAD_ERP\Obra;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
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

class SendRegistroSolicitudRecepcionCFDINotification
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
    public function handle(RegistroSolicitudRecepcionCFDI $event)
    {
        $suscripciones = Suscripcion::activa()->where("id_evento",$event->tipo)->get();
        $roles = ["contador","administrador_de_obra","comprador_compras","comprador_contratos","comprador_maquinaria","cuentas_por_pagar"];

        $usuarios_interesados_permisos = usuario::usuarioRol(
            $roles
            , $event->solicitud->id_proyecto_obra)->get();
        $usuario = Usuario::suscripcion($suscripciones, $event->solicitud->usuario_registro)->get();

        Notification::send($usuario, new NotificacionSolicitudRecepcionCFDI($event->solicitud));
        Notification::send($usuarios_interesados_permisos, new NotificacionSolicitudRecepcionCFDI($event->solicitud));
        Notification::route("mail",$event->solicitud->correo_notificaciones)->notify(new NotificacionSolicitudRecepcionCFDIProveedor($event->solicitud));
    }
}
