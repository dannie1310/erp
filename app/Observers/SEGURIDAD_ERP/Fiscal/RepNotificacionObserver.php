<?php
namespace App\Observers\SEGURIDAD_ERP\Fiscal;

use App\Models\SEGURIDAD_ERP\Fiscal\RepNotificacion;
use App\Notifications\NotificacionREP;

class RepNotificacionObserver
{
    /**
     * @param NotificacionREP $notificacion_rep
     * @return void
     */
    public function creating(RepNotificacion $notificacion_rep)
    {
        $notificacion_rep->usuario_registro = auth()->id();
    }
}
