<?php

namespace App\Notifications;

use App\CSV\Fiscal\ProveedoresREPPendiente;
use App\Models\CADECO\SolicitudCompra;
use App\Models\SEGURIDAD_ERP\Concursos\Concurso;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\Fiscal\ProveedorREP;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use App\PDF\Concurso\InformeCierre;
use App\PDF\Fiscal\Comunicado;
use App\PDF\PortalProveedores\InvitacionCotizarFormato;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NotificacionAperturaConcurso extends Notification
{
    use Queueable;
    public $proveedor;
    public $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Concurso $concurso, $token)
    {
        $this->concurso = $concurso;
        $this->token = $token;
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
        $titulo = "Apertura de Concurso ".$this->concurso->nombre;

        $pdf = new InformeCierre($this->concurso);

        return (new MailMessage)
            ->subject($titulo)
            ->view('emails.notificacion_apertura_concurso',["concurso"=>$this->concurso,"token"=>$this->token])
            ->attachData($pdf->Output("S", 'InformeApertura-'.$this->concurso->nombre_archivo.".pdf"), 'InformeApertura-'.$this->concurso->nombre_archivo . '.pdf',['mime' => 'application/pdf']);
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
