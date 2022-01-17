<?php

namespace App\Notifications;

use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use App\PDF\PortalProveedores\InvitacionCotizarFormato;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NotificacionInvitacionCotizarCopiados extends Notification
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
        if($this->invitacion->tipo == 2)
        {
            $titulo = "Invitacion a Contraofertar";
        }else
        {
            $titulo = "Invitacion a Cotizar";
        }

        $pdf = new InvitacionCotizarFormato($this->invitacion);
        return (new MailMessage)
            ->subject($titulo)
            ->view('emails.invitacion_cotizar_copiados',["invitacion"=>$this->invitacion])
            ->attachData($pdf->Output("S",$titulo . " ".$this->invitacion->transaccionAntecedente->numero_folio.".pdf"), $titulo . ' '.$this->invitacion->transaccionAntecedente->numero_folio.'.pdf',['mime' => 'application/pdf']);
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
