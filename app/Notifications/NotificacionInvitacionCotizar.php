<?php

namespace App\Notifications;

use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Contabilidad\CargaCFDSAT;
use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDI;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\LoteBusqueda;
use App\PDF\SolicitudRecepcionCFDI\SolicitudRecepcionCFDIPDF;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotificacionInvitacionCotizar extends Notification
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
        //$path0 = "uploads/contabilidad/XML_SAT/".$this->solicitud->cfdi->uuid.".xml";
        //$pdf = new SolicitudRecepcionCFDIPDF($this->solicitud);

        return (new MailMessage)
            ->subject("Invitación a cotizar de Grupo Hermes Infraestructura")
            ->view('emails.invitacion_cotizar',["invitacion"=>$this->invitacion]);

        /*if(file_exists($path0)){
            return (new MailMessage)
                ->subject("Solicitud de Recepción de CFDI Registrada")
                ->view('emails.solicitud_recepcion_cfdi',["solicitud"=>$this->solicitud])
                ->attach($path0)
                ->attachData($pdf->Output("S","solicitud_".$this->solicitud->numero_folio.".pdf"), 'solicitud_'.$this->solicitud->numero_folio.'.pdf',['mime' => 'application/pdf']);
        } else {
            return (new MailMessage)
                ->subject("Solicitud de Recepción de CFDI Registrada")
                ->view('emails.solicitud_recepcion_cfdi',["solicitud"=>$this->solicitud])
                ->attachData($pdf->Output("S","solicitud_".$this->solicitud->numero_folio.".pdf"), 'solicitud_'.$this->solicitud->numero_folio.'.pdf',['mime' => 'application/pdf']);
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
