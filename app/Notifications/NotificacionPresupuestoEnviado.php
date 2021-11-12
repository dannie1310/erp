<?php

namespace App\Notifications;

use App\Models\CADECO\CotizacionCompra;
use App\Models\CADECO\PresupuestoContratista;
use App\Models\CADECO\SolicitudCompra;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NotificacionPresupuestoEnviado extends Notification
{
    use Queueable;
    public $invitacion;
    public $cotizacion;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Invitacion $invitacion, PresupuestoContratista $cotizacion)
    {
        $this->invitacion = $invitacion;
        $this->cotizacion = $cotizacion;
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
        $carta_terminos = $this->cotizacion->archivos()->where("id_tipo_archivo","=",3)->first();
        if($carta_terminos){
            $path_carta_terminos = "uploads/archivos_transacciones/".$carta_terminos->hashfile.".".$carta_terminos->extension;
        }


        if(!file_exists($path_carta_terminos)){
            return (new MailMessage)
                ->subject("Cotización enviada por ".$this->cotizacion->empresa->rfc .' '. $this->cotizacion->empresa->razon_social)
                ->view('emails.cotizacion_enviada',["invitacion"=>$this->invitacion, "cotizacion"=>$this->cotizacion]);
        }else{
            return (new MailMessage)
                ->subject("Cotización enviada por ".$this->cotizacion->empresa->rfc .' '. $this->cotizacion->empresa->razon_social)
                ->view('emails.cotizacion_enviada',["invitacion"=>$this->invitacion, "cotizacion"=>$this->cotizacion])
                ->attach($path_carta_terminos,["as"=>$carta_terminos->tipoArchivo->descripcion.".".$carta_terminos->extension]);
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
