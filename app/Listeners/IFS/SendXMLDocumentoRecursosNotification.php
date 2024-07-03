<?php

namespace App\Listeners\IFS;

use App\Events\IFS\EnvioXMLDocumentoRecursos;
use App\Mail\NotificacionXMLDocumentoRecursosEnviada;
use Illuminate\Support\Facades\Mail;

class SendXMLDocumentoRecursosNotification
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
     * @param EnvioXMLDocumentoRecursos $event
     * @return void
     */
    public function handle(EnvioXMLDocumentoRecursos $event)
    {
        $destinatarios = $event->destinatarios;
        $bcc = [
            'dbenitezc@grupohi.mx','ebriones@grupohi.mx'
        ];
        Mail::to($destinatarios)->bcc($bcc)->sendNow(new NotificacionXMLDocumentoRecursosEnviada($event->documento, $event->xml, $event->archivo));
    }
}
