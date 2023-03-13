<?php

namespace App\Mail;

use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\Fiscal\ProveedorREP;
use App\Models\SEGURIDAD_ERP\Fiscal\RepNotificacion;
use App\PDF\Fiscal\Comunicado;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificacionREP extends Mailable
{
    use Queueable, SerializesModels;

    public $notificacion;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(RepNotificacion $notificacion)
    {
        $this->notificacion = $notificacion;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $titulo = "Hermes Infraestructura - Recibos Electrónicos de Pagos (REP) pendientes de emisión";

        $proveedor = ProveedorREP::find($this->notificacion->id_proveedor_sat);

        $uuids = $proveedor->cfdi()->repPendiente()->get();
        $arr_comunicados = [];
        foreach ($uuids as $uuid)
        {
            $arr_comunicados["proveedor"] = $proveedor->proveedor;
            $arr_comunicados["receptores"][$uuid->rfc_receptor]["empresa"] = $uuid->empresa->razon_social;
            $arr_comunicados["receptores"][$uuid->rfc_receptor]["uuid"][] = $uuid;
        }

        $pdf = new Comunicado($arr_comunicados);

        return $this
            ->subject($titulo)
            ->view('emails.notificacion_rep',["cuerpo_correo"=>$this->notificacion->cuerpo_correo])
            ->attachData($pdf->Output("S", 'Comunicado-'.$proveedor->rfc_proveedor.".pdf"), 'Comunicado-'.$proveedor->rfc_proveedor . '.pdf',['mime' => 'application/pdf']);

    }
}
