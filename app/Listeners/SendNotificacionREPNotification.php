<?php

namespace App\Listeners;


use App\Events\RegistroNotificacionREP;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Mail\NotificacionREP;
use Illuminate\Support\Facades\Mail;


class SendNotificacionREPNotification
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
     * @param RegistroNotificacionREP $event
     * @return void
     */
    public function handle(RegistroNotificacionREP $event)
    {
        $suscripciones = Suscripcion::activa()->where("id_evento", 18)->get();
        $usuarios_suscritos = Usuario::suscripcion($suscripciones)->get();
        foreach ($usuarios_suscritos as $usuario_suscrito) {
            $event->notificacion->destinatarios()->create([
                "id_usuario_hermes" => $usuario_suscrito->idusuario,
                "correo" => $usuario_suscrito->correo,
                "nombre" => $usuario_suscrito->usuario,
            ]);
        }

        $ubicaciones_rep = $event->notificacion->proveedor->ubicacionesRep;

        foreach ($ubicaciones_rep as $ubicacion_rep)
        {
            if($ubicacion_rep->obraGlobal && $ubicacion_rep->obraGlobal->administrador)
            {
                $event->notificacion->destinatarios()->create([
                    "id_usuario_hermes" => $ubicacion_rep->obraGlobal->id_administrador,
                    "correo" => $ubicacion_rep->obraGlobal->administrador->correo,
                    "nombre" => $ubicacion_rep->obraGlobal->administrador->nombre_completo,
                ]);
            }

            if($ubicacion_rep->obraGlobal && $ubicacion_rep->obraGlobal->responsable)
            {
                $event->notificacion->destinatarios()->create([
                    "id_usuario_hermes" => $ubicacion_rep->obraGlobal->id_responsable,
                    "correo" => $ubicacion_rep->obraGlobal->responsable->correo,
                    "nombre" => $ubicacion_rep->obraGlobal->responsable->nombre_completo,
                ]);
            }
        }

        $destinatarios = $event->notificacion->destinatarios()->proveedor()->pluck("correo");
        $destinatarios_copiados = $event->notificacion->destinatarios()->hermes()->pluck("correo");

        Mail::to($destinatarios)->cc($destinatarios_copiados)->queue(new NotificacionREP($event->notificacion->proveedor));
    }
}
