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
        if(!empty($destinatarios)) {
            $bcc = [
                'dbenitezc@grupohi.mx','ebriones@grupohi.mx', 'jegarcia@grupohi.mx', 'melievana@grupohi.mx'
            ];
            try {
                Mail::to($destinatarios)->bcc($bcc)->send(new NotificacionXMLDocumentoRecursosEnviada($event->documento, $event->xml, $event->archivo));
            }catch (\Exception $e)
            {
                abort(500, "Error en el envío de correo : ".$e->getMessage());
            }
        }else{
            abort(500, "No existen destinatarios para este envío de correo.");
        }
    }
}
