<?php

namespace App\Notifications;

use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NotificacionInvitacionAbierta extends Notification
{
    use Queueable;
    public $invitacion;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Invitacion $invitacion)
    {
        $this->invitacion = $invitacion;
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
            ->subject("Invitación ".$this->invitacion->numero_folio_format." Abierta")
            ->view('emails.invitacion_abierta',["invitacion"=>$this->invitacion]);
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
