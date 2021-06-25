<?php

namespace App\Notifications;

use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCFDI;
use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\LoteBusqueda;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotificacionFinalizaProcesoAsociacionPolizasCFDI extends Notification
{
    use Queueable;
    public $solicitud_asociacion;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(SolicitudAsociacionCFDI $solicitud_asociacion)
    {
        $this->solicitud_asociacion = $solicitud_asociacion;

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
            ->subject("Finalización de Proceso de Asociación de Pólizas vs CFDI")
            ->view('emails.solicitud_asociacion_cfdi_polizas',["solicitud"=>$this->solicitud_asociacion]);
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
