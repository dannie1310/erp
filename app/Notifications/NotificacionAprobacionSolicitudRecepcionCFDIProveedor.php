<?php

namespace App\Notifications;

use App\Models\SEGURIDAD_ERP\Contabilidad\CargaCFDSAT;
use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDI;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\LoteBusqueda;
use App\PDF\Finanzas\ContrareciboPDF;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotificacionAprobacionSolicitudRecepcionCFDIProveedor extends Notification
{
    use Queueable;
    public $solicitud;
    public $id_factura;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(SolicitudRecepcionCFDI $solicitud, $id_factura)
    {
        $this->solicitud = $solicitud;
        $this->id_factura = $id_factura;
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
        $pdf = new ContrareciboPDF($this->id_factura);

        if(file_exists($path0)){
            return (new MailMessage)
                ->subject("Aprobación de Solicitud de Recepción de CFDI")
                ->view('emails.aprobacion_solicitud_recepcion_cfdi_proveedor',["solicitud"=>$this->solicitud])
                ->attach($path0)
                ->attachData($pdf->Output("S","contrarecibo_".$this->id_factura.".pdf"), 'contrarecibo_'.$this->id_factura.'.pdf',['mime' => 'application/pdf']);
        } else {
            return (new MailMessage)
                ->subject("Aprobación de Solicitud de Recepción de CFDI")
                ->view('emails.aprobacion_solicitud_recepcion_cfdi_proveedor',["solicitud"=>$this->solicitud])
                ->attachData($pdf->Output("S","contrarecibo_".$this->id_factura.".pdf"), 'contrarecibo_'.$this->id_factura.'.pdf',['mime' => 'application/pdf']);
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
