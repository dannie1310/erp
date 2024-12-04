<?php

namespace App\Mail;

use App\Models\CTPQ\NmNominas\Nom10015;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionXMLPolizaNominas extends Mailable
{
    use Queueable, SerializesModels;

    public $poliza;
    public $xml;
    public $archivo;


    /**
     * @param $archivo
     * @param $xml
     */
    public function __construct(Nom10015 $poliza, $xml,$archivo)
    {
        $this->poliza = $poliza;
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
        $titulo = "IFS Póliza Nóminas";
        return $this
            ->subject($titulo)
            ->view('emails.envio_xml_poliza_nominas',["poliza" => $this->poliza])
            ->attach($this->archivo, ["as" => $this->xml]);
    }
}
