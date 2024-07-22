<?php

namespace App\Mail;

use App\Models\CONTROL_RECURSOS\Documento;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificacionXMLDocumentoRecursosEnviada extends Mailable
{
    use Queueable, SerializesModels;

    public $documento;
    public $xml;
    public $archivo;


    /**
     * @param $archivo
     * @param $xml
     */
    public function __construct(Documento $documento, $xml,$archivo)
    {
        $this->documento = $documento;
        $this->xml = $xml;
        $this->archivo = $archivo;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $titulo = "IFS Factura";
        return $this
            ->subject($titulo)
            ->view('emails.envio_xml_documento_recursos',["documento" => $this->documento])
            ->attach($this->archivo, ["as" => $this->xml]);
    }
}
