<?php

namespace App\Notifications;

use App\Mail\IncidenciaCI;
use App\Models\CADECO\Solicitud;
use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudPagoAutorizacion;
use App\PDF\Finanzas\PagoAnticipado;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotificacionRechazoPagoAnticipado extends Notification
{
    use Queueable;

    public $solicitud_pago_autorizacion;
    public $solicitud_pago_anticipado;
    public $motivo;

    public function __construct(SolicitudPagoAutorizacion $solicitud_pago_autorizacion, SolicitudPagoAnticipado $solicitud_pago_anticipado, $motivo)
    {
        $this->solicitud_pago_autorizacion = $solicitud_pago_autorizacion;
        $this->solicitud_pago_anticipado = $solicitud_pago_anticipado;
        $this->motivo = $motivo;
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
        return (new MailMessage)
            ->subject("Rechazo de Pago Anticipado ".$this->solicitud_pago_anticipado->numero_folio_format . " (".$this->solicitud_pago_anticipado->obra->nombre.")")
            ->view('emails.rechazo_pago_anticipado',[
                "solicitud_pago_autorizacion"=>$this->solicitud_pago_autorizacion,
                "solicitud_pago_anticipado"=>$this->solicitud_pago_anticipado,
                "motivo_rechazo"=>$this->motivo
            ]);
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
