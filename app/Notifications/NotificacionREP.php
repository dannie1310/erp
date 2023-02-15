<?php

namespace App\Notifications;

use App\CSV\Fiscal\ProveedoresREPPendiente;
use App\Models\CADECO\SolicitudCompra;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\Fiscal\ProveedorREP;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use App\PDF\Fiscal\Comunicado;
use App\PDF\PortalProveedores\InvitacionCotizarFormato;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NotificacionREP extends Notification
{
    use Queueable;
    public $proveedor;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ProveedorSAT $proveedor)
    {
        $this->proveedor = $proveedor;
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
        $titulo = "REPs Pendientes";

        $proveedor = ProveedorREP::find($this->proveedor->id);

        $uuids = $proveedor->cfdi()->repPendiente()->get();
        $arr_comunicados = [];
        foreach ($uuids as $uuid)
        {
            $arr_comunicados["proveedor"] = $uuid->proveedor->razon_social;
            $arr_comunicados["receptores"][$uuid->rfc_receptor]["empresa"] = $uuid->empresa->razon_social;
            $arr_comunicados["receptores"][$uuid->rfc_receptor]["uuid"][] = $uuid;
        }

        $pdf = new Comunicado($arr_comunicados);

        return (new MailMessage)
            ->subject($titulo)
            ->view('emails.notificacion_rep',["proveedor"=>$this->proveedor])
            ->attachData($pdf->Output("S", 'Comunicado-'.$this->proveedor->rfc.".pdf"), 'Comunicado-'.$this->proveedor->rfc . '.pdf',['mime' => 'application/pdf']);
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
