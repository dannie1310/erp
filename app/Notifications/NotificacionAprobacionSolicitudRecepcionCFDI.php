<?php

namespace App\Notifications;

use App\Models\SEGURIDAD_ERP\Contabilidad\CargaCFDSAT;
use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDI;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\LoteBusqueda;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotificacionAprobacionSolicitudRecepcionCFDI extends Notification
{
    use Queueable;
    public $solicitud;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(SolicitudRecepcionCFDI $solicitud)
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
        $path0 = "uploads/contabilidad/XML_SAT/".$this->solicitud->cfdi->uuid.".xml";

        if(file_exists($path0)){
            return (new MailMessage)
                ->subject("Aprobación de Solicitud de Recepción de CFDI")
                ->view('emails.aprobacion_solicitud_recepcion_cfdi',["solicitud"=>$this->solicitud])
                ->attach($path0);
        } else {
            return (new MailMessage)
                ->subject("Aprobación de Solicitud de Recepción de CFDI")
                ->view('emails.aprobacion_solicitud_recepcion_cfdi',["solicitud"=>$this->solicitud]);
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
