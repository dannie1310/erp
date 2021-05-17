<?php


namespace App\Listeners;


use App\Events\CambioNoLocalizados;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Notifications\NotificacionCambioNoLocalizados;
use Illuminate\Support\Facades\Notification;

class SendCambiosNoLocalizadosNotification
{
    public function handle(CambioNoLocalizados $event)
    {
        $suscripciones = Suscripcion::activa()->where("id_evento",$event->tipo)->get();
        $usuario = Usuario::suscripcion($suscripciones)->get();
        Notification::send($usuario, new NotificacionCambioNoLocalizados($event->altas, $event->bajas));
    }

}
