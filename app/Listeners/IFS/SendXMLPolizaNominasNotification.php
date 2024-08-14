<?php

namespace App\Listeners\IFS;

use App\Events\IFS\EnvioXMLPolizaNominas;
use App\Mail\NotificacionXMLPolizaNominas;
use Illuminate\Support\Facades\Mail;

class SendXMLPolizaNominasNotification
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
     * @param EnvioXMLPolizaNominas $event
     * @return void
     */
    public function handle(EnvioXMLPolizaNominas $event)
    {
        $destinatarios = $event->destinatarios;
        $bcc = [
            'dbenitezc@grupohi.mx','ebriones@grupohi.mx', 'jegarcia@grupohi.mx'
        ];
        Mail::to($destinatarios)->bcc($bcc)->sendNow(new NotificacionXMLPolizaNominas($event->poliza, $event->xml, $event->archivo));
    }
}
