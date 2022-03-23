<?php

namespace App\Notifications\Messages;

use App\Mail\IncidenciaCI;
use App\Models\CADECO\Solicitud;
use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use App\PDF\Finanzas\PagoAnticipado;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotificacionSolicitudAutorizacionPagoAnticipadoSMS extends Notification
{
    use Queueable;
    public $solicitud;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(SolicitudPagoAnticipado $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['sms'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toNexmo($notifiable)
    {
        $pdf = new PagoAnticipado($this->solicitud->id_transaccion);
        return (new MailMessage)
            ->subject("Solicitud de Autorización de Pago Anticipado")
            ->view('emails.solicitud_pago_anticipado',["solicitud"=>$this->solicitud])
            ->attachData($pdf->Output("S","solicitud_pago_anticipado_".$this->solicitud->numero_folio.".pdf"), 'solicitud_pago_anticipado_'.$this->solicitud->numero_folio.'.pdf',['mime' => 'application/pdf']);
            /*->attach($this->xml);*/
    }

}
