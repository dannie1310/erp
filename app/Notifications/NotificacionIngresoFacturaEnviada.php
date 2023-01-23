<?php

namespace App\Notifications;

use App\Models\REPSEG\FinFacIngresoFactura;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotificacionIngresoFacturaEnviada extends Notification
{
    use Queueable;
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
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if($this->xml == null)
        {
            return (new MailMessage)
                ->subject("Factura Registrada (" . $this->factura->proyecto->proyecto . ').')
                ->cc(Array($this->factura->getCCNotificacionIngreso()))
                ->bcc(Array($this->factura->getCCONotificacionIngreso()))
                ->view('emails.ingreso_factura_registrada', ["factura" => $this->factura])
                ->attach($this->archivo, ["as" => $this->factura->numero . ".pdf"]);
        }else{
            return (new MailMessage)
                ->subject("Factura Registrada (" . $this->factura->proyecto->proyecto . ').')
                ->cc(Array($this->factura->getCCNotificacionIngreso()))
                ->bcc(Array($this->factura->getCCONotificacionIngreso()))
                ->view('emails.ingreso_factura_registrada', ["factura" => $this->factura])
                ->attach($this->archivo, ["as" => $this->factura->uuid . ".pdf"])
                ->attach($this->xml, ["as" => $this->factura->uuid . ".xml"]);
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
