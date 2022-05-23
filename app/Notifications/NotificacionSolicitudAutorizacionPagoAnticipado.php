<?php

namespace App\Notifications;

use App\Mail\IncidenciaCI;
use App\Models\CADECO\Solicitud;
use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use App\PDF\Finanzas\PagoAnticipado;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotificacionSolicitudAutorizacionPagoAnticipado extends Notification
{
    use Queueable;
    public $solicitud;
    public $token;
    public $nombre_usuario;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(SolicitudPagoAnticipado $solicitud, $token = null, $nombre_usuario = null)
    {
        $this->solicitud = $solicitud;
        $this->token = $token;
        $this->nombre_usuario = $nombre_usuario;
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
        $pdf = new PagoAnticipado($this->solicitud->id_transaccion);
        return (new MailMessage)
            ->subject("Solicitud de Autorización de Pago Anticipado")
            ->view('emails.solicitud_pago_anticipado',["solicitud"=>$this->solicitud, "token"=>$this->token, "nombre_usuario"=>$this->nombre_usuario, "usuario_registro"=>auth()->user()->nombre_completo])
            ->attachData($pdf->Output("S","solicitud_pago_anticipado_".$this->solicitud->numero_folio.".pdf"), 'solicitud_pago_anticipado_'.$this->solicitud->numero_folio.'.pdf',['mime' => 'application/pdf']);
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
