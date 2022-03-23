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
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class NotificacionSolicitudAutorizacionPagoAnticipadoSinContexto extends Notification
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
    public function __construct(SolicitudPagoAutorizacion $solicitud, $token = null, $nombre_usuario = null)
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
        //DB::purge('cadeco');
        //Config::set('database.connections.cadeco.database', $this->solicitud->base_datos);
        //$pdf = new PagoAnticipado($this->solicitud->id_transaccion);
        return (new MailMessage)
            ->subject("Solicitud de Autorización de Pago Anticipado")
            ->view('emails.solicitud_pago_anticipado_sin_contexto',["solicitud"=>$this->solicitud, "token"=>$this->token, "nombre_usuario"=>$this->nombre_usuario]);
            //->attachData($pdf->Output("S","solicitud_pago_anticipado_".$this->solicitud->numero_folio.".pdf"), 'solicitud_pago_anticipado_'.$this->solicitud->numero_folio.'.pdf',['mime' => 'application/pdf']);
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
