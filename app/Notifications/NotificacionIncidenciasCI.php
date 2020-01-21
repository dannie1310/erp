<?php

namespace App\Notifications;

use App\Mail\IncidenciaCI;
use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotificacionIncidenciasCI extends Notification
{
    use Queueable;
    public $incidencia;
    public $xml;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Incidencia $incidencia, $xml)
    {
        $this->incidencia = $incidencia;
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

        return (new MailMessage)
            ->subject("Incidencia Control Interno")
            ->view('emails.incidenciaci',["incidencia"=>$this->incidencia]);
            /*->attach($this->xml);*/
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
