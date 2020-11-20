<?php


namespace App\Notifications;


use App\Models\SEGURIDAD_ERP\Contabilidad\CargaCFDSAT;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotificacionCambioEFOS extends Notification
{
    use Queueable;
    public $cambios;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($cambios)
    {
        $this->cambios = $cambios;
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
            ->subject("Cambios EFOS")
            ->view('emails.cambios_efos',["cambios"=>$this->cambios]);
        /*$path0 = "uploads/contabilidad/XML_errores/".$this->carga->id.".zip";

        if(file_exists($path0)){
            return (new MailMessage)
                ->subject("Finalización de Carga de CFD")
                ->view('emails.carga_cfd',["carga"=>$this->carga])
                ->attach($path0);
        } else {
            return (new MailMessage)
                ->subject("Finalización de Carga de CFD")
                ->view('emails.carga_cfd',["carga"=>$this->carga]);
        }*/


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
