<?php

namespace App\Mail;

use App\Models\REPSEG\FinFacIngresoFactura;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificacionIngresoFacturaEnviada extends Mailable
{
    use Queueable, SerializesModels;

    public $factura;
    public $archivo;
    public $xml;


    /**
     * @param $factura
     * @param $archivo
     */
    public function __construct(FinFacIngresoFactura $factura, $archivo, $xml)
    {
        $this->factura = $factura;
        $this->archivo = $archivo;
        $this->xml = $xml;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $titulo = "Factura Registrada (" . $this->factura->proyecto->proyecto . ').';
        if($this->xml == null && $this->archivo != null)
        {
            return $this
                ->subject($titulo)
                ->view('emails.ingreso_factura_registrada',["factura" => $this->factura])
                ->attach($this->archivo, ["as" => $this->factura->numero . ".pdf"]);
        }
        elseif($this->xml != null && $this->archivo != null){
            return $this
                ->subject($titulo)
                ->view('emails.ingreso_factura_registrada',["factura" => $this->factura])
                ->attach($this->archivo, ["as" => $this->factura->uuid . ".pdf"])
                ->attach($this->xml, ["as" => $this->factura->uuid . ".xml"]);
        }
        elseif($this->xml != null && $this->archivo == null){
            return $this
                ->subject($titulo)
                ->view('emails.ingreso_factura_registrada',["factura" => $this->factura])
                ->attach($this->xml, ["as" => $this->factura->uuid . ".xml"]);
        }
        else{
            return $this
                ->subject($titulo)
                ->view('emails.ingreso_factura_registrada',["factura" => $this->factura]);
        }
    }
}
