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

        $destinatarios = $event->notificacion->destinatarios()->proveedor()->pluck("correo");
        $destinatarios_copiados = $event->notificacion->destinatarios()->hermes()->pluck("correo");


        Mail::to($destinatarios)->cc($destinatarios_copiados)->send(new NotificacionREP($event->notificacion->proveedor));
    }
}
