<?php


namespace App\Listeners;


use App\Events\CambioEFOS;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Notifications\NotificacionCambioEFOS;
use Illuminate\Support\Facades\Notification;

class SendCambiosEFOSNotification
{
    public function handle(CambioEFOS $event)
    {
        $suscripciones = Suscripcion::activa()->where("id_evento",$event->tipo)->get();
        $usuario = Usuario::suscripcion($suscripciones)->get();
        Notification::send($usuario, new NotificacionCambioEFOS($event->cambios));
    }

}
