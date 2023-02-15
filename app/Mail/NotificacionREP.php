<?php

namespace App\Mail;

use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\Fiscal\ProveedorREP;
use App\PDF\Fiscal\Comunicado;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificacionREP extends Mailable
{
    use Queueable, SerializesModels;

    public $proveedor;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ProveedorSAT $proveedor)
    {
        $this->proveedor = $proveedor;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
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

        return $this
            ->subject($titulo)
            ->view('emails.notificacion_rep',["proveedor"=>$this->proveedor])
            ->attachData($pdf->Output("S", 'Comunicado-'.$this->proveedor->rfc.".pdf"), 'Comunicado-'.$this->proveedor->rfc . '.pdf',['mime' => 'application/pdf']);

    }
}
