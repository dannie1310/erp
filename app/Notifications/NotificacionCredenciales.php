<?php

namespace App\Notifications;

use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Contabilidad\CargaCFDSAT;
use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDI;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\LoteBusqueda;
use App\PDF\SolicitudRecepcionCFDI\SolicitudRecepcionCFDIPDF;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotificacionCredenciales extends Notification
{
    use Queueable;
    public $usuario;
    public $clave;
    public $reset;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Usuario $usuario, $clave, $reset)
    {
        $this->usuario = $usuario;
        $this->clave = $clave;
        $this->reset = $reset;
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
            ->subject(is_null($this->usuario->id_empresa_invito) ? "Datos de acceso al portal de aplicaciones e intranet" : "Datos de acceso al portal de proveedores de Hermes Infraestructura")
            ->view('emails.datos_acceso',["usuario"=>$this->usuario->usuario,"clave"=>$this->clave, "reset"=>$this->reset]);

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
