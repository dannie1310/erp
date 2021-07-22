<?php

namespace App\Notifications;

use App\Models\CADECO\SolicitudCompra;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use App\PDF\CADECO\Compras\SolicitudCompraFormato;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
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
        $carta_terminos = $this->invitacion->archivos()->where("id_tipo_archivo","=",43)->first();
        if($carta_terminos){
            $path_carta_terminos = "uploads/archivos_transacciones/".$carta_terminos->hashfile.".".$carta_terminos->extension;
        }

        $formato_cotizacion = $this->invitacion->archivos()->where("id_tipo_archivo","=",44)->first();
        $path_formato_cotizacion = null;
        if($formato_cotizacion)
        {
            $path_formato_cotizacion = "uploads/archivos_transacciones/".$formato_cotizacion->hashfile.".".$formato_cotizacion->extension;
        }

        $tamanio_mb = 12;

        if($this->invitacion->transaccionAntecedente->tipo_transaccion == 17){
            //0000
            if(!file_exists($path_carta_terminos)  && !(($carta_terminos->tamanio_kb/1024)<$tamanio_mb) && !file_exists($path_formato_cotizacion) && !(($formato_cotizacion->tamanio_kb/1024)<$tamanio_mb)){
                $solicitud = SolicitudCompra::find($this->invitacion->transaccionAntecedente->id_transaccion);
                $pdf = new SolicitudCompraFormato($solicitud);
                return (new MailMessage)
                    ->subject("Solicitud de Cotización")
                    ->view('emails.invitacion_cotizar',["invitacion"=>$this->invitacion])
                    ->attachData($pdf->Output("S","solicitud_compra_".$this->invitacion->transaccionAntecedente->numero_folio.".pdf"), 'solicitud_compra_'.$this->invitacion->transaccionAntecedente->numero_folio.'.pdf',['mime' => 'application/pdf']);
            }
            //0001
            else if(!file_exists($path_carta_terminos)  && !(($carta_terminos->tamanio_kb/1024)<$tamanio_mb) && !file_exists($path_formato_cotizacion) && (($formato_cotizacion->tamanio_kb/1024)<$tamanio_mb)){
                $solicitud = SolicitudCompra::find($this->invitacion->transaccionAntecedente->id_transaccion);
                $pdf = new SolicitudCompraFormato($solicitud);
                return (new MailMessage)
                    ->subject("Solicitud de Cotización")
                    ->view('emails.invitacion_cotizar',["invitacion"=>$this->invitacion])
                    ->attachData($pdf->Output("S","solicitud_compra_".$this->invitacion->transaccionAntecedente->numero_folio.".pdf"), 'solicitud_compra_'.$this->invitacion->transaccionAntecedente->numero_folio.'.pdf',['mime' => 'application/pdf']);
            }
            //0010
            else if(!file_exists($path_carta_terminos)  && !(($carta_terminos->tamanio_kb/1024)<$tamanio_mb) && file_exists($path_formato_cotizacion) && !(($formato_cotizacion->tamanio_kb/1024)<$tamanio_mb)){
                $solicitud = SolicitudCompra::find($this->invitacion->transaccionAntecedente->id_transaccion);
                $pdf = new SolicitudCompraFormato($solicitud);
                return (new MailMessage)
                    ->subject("Solicitud de Cotización")
                    ->view('emails.invitacion_cotizar',["invitacion"=>$this->invitacion])
                    ->attachData($pdf->Output("S","solicitud_compra_".$this->invitacion->transaccionAntecedente->numero_folio.".pdf"), 'solicitud_compra_'.$this->invitacion->transaccionAntecedente->numero_folio.'.pdf',['mime' => 'application/pdf']);
            }
            //0011
            else if(!file_exists($path_carta_terminos)  && !(($carta_terminos->tamanio_kb/1024)<$tamanio_mb) && file_exists($path_formato_cotizacion) && (($formato_cotizacion->tamanio_kb/1024)<$tamanio_mb)){
                $solicitud = SolicitudCompra::find($this->invitacion->transaccionAntecedente->id_transaccion);
                $pdf = new SolicitudCompraFormato($solicitud);
                return (new MailMessage)
                    ->subject("Solicitud de Cotización")
                    ->view('emails.invitacion_cotizar',["invitacion"=>$this->invitacion])
                    ->attach($path_formato_cotizacion,["as"=>$formato_cotizacion->tipo->descripcion.".".$formato_cotizacion->extension])
                    ->attachData($pdf->Output("S","solicitud_compra_".$this->invitacion->transaccionAntecedente->numero_folio.".pdf"), 'solicitud_compra_'.$this->invitacion->transaccionAntecedente->numero_folio.'.pdf',['mime' => 'application/pdf']);
            }
            //0100
            else if(!file_exists($path_carta_terminos)  && (($carta_terminos->tamanio_kb/1024)<$tamanio_mb) && !file_exists($path_formato_cotizacion) && !(($formato_cotizacion->tamanio_kb/1024)<$tamanio_mb)){
                $solicitud = SolicitudCompra::find($this->invitacion->transaccionAntecedente->id_transaccion);
                $pdf = new SolicitudCompraFormato($solicitud);
                return (new MailMessage)
                    ->subject("Solicitud de Cotización")
                    ->view('emails.invitacion_cotizar',["invitacion"=>$this->invitacion])
                    ->attachData($pdf->Output("S","solicitud_compra_".$this->invitacion->transaccionAntecedente->numero_folio.".pdf"), 'solicitud_compra_'.$this->invitacion->transaccionAntecedente->numero_folio.'.pdf',['mime' => 'application/pdf']);
            }
            //0101
            else if(!file_exists($path_carta_terminos)  && (($carta_terminos->tamanio_kb/1024)<$tamanio_mb) && !file_exists($path_formato_cotizacion) && (($formato_cotizacion->tamanio_kb/1024)<$tamanio_mb)){
                $solicitud = SolicitudCompra::find($this->invitacion->transaccionAntecedente->id_transaccion);
                $pdf = new SolicitudCompraFormato($solicitud);
                return (new MailMessage)
                    ->subject("Solicitud de Cotización")
                    ->view('emails.invitacion_cotizar',["invitacion"=>$this->invitacion])
                    ->attachData($pdf->Output("S","solicitud_compra_".$this->invitacion->transaccionAntecedente->numero_folio.".pdf"), 'solicitud_compra_'.$this->invitacion->transaccionAntecedente->numero_folio.'.pdf',['mime' => 'application/pdf']);
            }
            //0110
            else if(!file_exists($path_carta_terminos)  && (($carta_terminos->tamanio_kb/1024)<$tamanio_mb) && file_exists($path_formato_cotizacion) && !(($formato_cotizacion->tamanio_kb/1024)<$tamanio_mb)){
                $solicitud = SolicitudCompra::find($this->invitacion->transaccionAntecedente->id_transaccion);
                $pdf = new SolicitudCompraFormato($solicitud);
                return (new MailMessage)
                    ->subject("Solicitud de Cotización")
                    ->view('emails.invitacion_cotizar',["invitacion"=>$this->invitacion])
                    ->attach($path_formato_cotizacion,["as"=>$formato_cotizacion->tipo->descripcion.".".$formato_cotizacion->extension])
                    ->attachData($pdf->Output("S","solicitud_compra_".$this->invitacion->transaccionAntecedente->numero_folio.".pdf"), 'solicitud_compra_'.$this->invitacion->transaccionAntecedente->numero_folio.'.pdf',['mime' => 'application/pdf']);
            }
            //0111
            else if(!file_exists($path_carta_terminos)  && (($carta_terminos->tamanio_kb/1024)<$tamanio_mb) && file_exists($path_formato_cotizacion) && (($formato_cotizacion->tamanio_kb/1024)<$tamanio_mb)){
                $solicitud = SolicitudCompra::find($this->invitacion->transaccionAntecedente->id_transaccion);
                $pdf = new SolicitudCompraFormato($solicitud);
                return (new MailMessage)
                    ->subject("Solicitud de Cotización")
                    ->view('emails.invitacion_cotizar',["invitacion"=>$this->invitacion])
                    ->attach($path_formato_cotizacion,["as"=>$formato_cotizacion->tipo->descripcion.".".$formato_cotizacion->extension])
                    ->attachData($pdf->Output("S","solicitud_compra_".$this->invitacion->transaccionAntecedente->numero_folio.".pdf"), 'solicitud_compra_'.$this->invitacion->transaccionAntecedente->numero_folio.'.pdf',['mime' => 'application/pdf']);
            }
            //1000
            else if(file_exists($path_carta_terminos)  && !(($carta_terminos->tamanio_kb/1024)<$tamanio_mb) && !file_exists($path_formato_cotizacion) && !(($formato_cotizacion->tamanio_kb/1024)<$tamanio_mb)){
                $solicitud = SolicitudCompra::find($this->invitacion->transaccionAntecedente->id_transaccion);
                $pdf = new SolicitudCompraFormato($solicitud);
                return (new MailMessage)
                    ->subject("Solicitud de Cotización")
                    ->view('emails.invitacion_cotizar',["invitacion"=>$this->invitacion])
                    ->attachData($pdf->Output("S","solicitud_compra_".$this->invitacion->transaccionAntecedente->numero_folio.".pdf"), 'solicitud_compra_'.$this->invitacion->transaccionAntecedente->numero_folio.'.pdf',['mime' => 'application/pdf']);
            }
            //1001
            else if(file_exists($path_carta_terminos)  && !(($carta_terminos->tamanio_kb/1024)<$tamanio_mb) && !file_exists($path_formato_cotizacion) && (($formato_cotizacion->tamanio_kb/1024)<$tamanio_mb)){
                $solicitud = SolicitudCompra::find($this->invitacion->transaccionAntecedente->id_transaccion);
                $pdf = new SolicitudCompraFormato($solicitud);
                return (new MailMessage)
                    ->subject("Solicitud de Cotización")
                    ->view('emails.invitacion_cotizar',["invitacion"=>$this->invitacion])
                    ->attachData($pdf->Output("S","solicitud_compra_".$this->invitacion->transaccionAntecedente->numero_folio.".pdf"), 'solicitud_compra_'.$this->invitacion->transaccionAntecedente->numero_folio.'.pdf',['mime' => 'application/pdf']);
            }
            //1010
            else if(file_exists($path_carta_terminos)  && !(($carta_terminos->tamanio_kb/1024)<$tamanio_mb) && file_exists($path_formato_cotizacion) && !(($formato_cotizacion->tamanio_kb/1024)<$tamanio_mb)){
                $solicitud = SolicitudCompra::find($this->invitacion->transaccionAntecedente->id_transaccion);
                $pdf = new SolicitudCompraFormato($solicitud);
                return (new MailMessage)
                    ->subject("Solicitud de Cotización")
                    ->view('emails.invitacion_cotizar',["invitacion"=>$this->invitacion])
                    ->attachData($pdf->Output("S","solicitud_compra_".$this->invitacion->transaccionAntecedente->numero_folio.".pdf"), 'solicitud_compra_'.$this->invitacion->transaccionAntecedente->numero_folio.'.pdf',['mime' => 'application/pdf']);
            }
            //1011
            else if(file_exists($path_carta_terminos)  && !(($carta_terminos->tamanio_kb/1024)<$tamanio_mb) && file_exists($path_formato_cotizacion) && (($formato_cotizacion->tamanio_kb/1024)<$tamanio_mb)){
                $solicitud = SolicitudCompra::find($this->invitacion->transaccionAntecedente->id_transaccion);
                $pdf = new SolicitudCompraFormato($solicitud);
                return (new MailMessage)
                    ->subject("Solicitud de Cotización")
                    ->view('emails.invitacion_cotizar',["invitacion"=>$this->invitacion])
                    ->attach($path_formato_cotizacion,["as"=>$formato_cotizacion->tipo->descripcion.".".$formato_cotizacion->extension])
                    ->attachData($pdf->Output("S","solicitud_compra_".$this->invitacion->transaccionAntecedente->numero_folio.".pdf"), 'solicitud_compra_'.$this->invitacion->transaccionAntecedente->numero_folio.'.pdf',['mime' => 'application/pdf']);
            }
            //1100
            else if(file_exists($path_carta_terminos)  && (($carta_terminos->tamanio_kb/1024)<$tamanio_mb) && !file_exists($path_formato_cotizacion) && !(($formato_cotizacion->tamanio_kb/1024)<$tamanio_mb)){
                $solicitud = SolicitudCompra::find($this->invitacion->transaccionAntecedente->id_transaccion);
                $pdf = new SolicitudCompraFormato($solicitud);
                return (new MailMessage)
                    ->subject("Solicitud de Cotización")
                    ->view('emails.invitacion_cotizar',["invitacion"=>$this->invitacion])
                    ->attach($path_carta_terminos,["as"=>$carta_terminos->tipo->descripcion.".".$carta_terminos->extension])
                    ->attachData($pdf->Output("S","solicitud_compra_".$this->invitacion->transaccionAntecedente->numero_folio.".pdf"), 'solicitud_compra_'.$this->invitacion->transaccionAntecedente->numero_folio.'.pdf',['mime' => 'application/pdf']);
            }
            //1101
            else if(file_exists($path_carta_terminos)  && (($carta_terminos->tamanio_kb/1024)<$tamanio_mb) && !file_exists($path_formato_cotizacion) && (($formato_cotizacion->tamanio_kb/1024)<$tamanio_mb)){
                $solicitud = SolicitudCompra::find($this->invitacion->transaccionAntecedente->id_transaccion);
                $pdf = new SolicitudCompraFormato($solicitud);
                return (new MailMessage)
                    ->subject("Solicitud de Cotización")
                    ->view('emails.invitacion_cotizar',["invitacion"=>$this->invitacion])
                    ->attach($path_carta_terminos,["as"=>$carta_terminos->tipo->descripcion.".".$carta_terminos->extension])
                    ->attachData($pdf->Output("S","solicitud_compra_".$this->invitacion->transaccionAntecedente->numero_folio.".pdf"), 'solicitud_compra_'.$this->invitacion->transaccionAntecedente->numero_folio.'.pdf',['mime' => 'application/pdf']);
            }
            //1110
            else if(file_exists($path_carta_terminos)  && (($carta_terminos->tamanio_kb/1024)<$tamanio_mb) && file_exists($path_formato_cotizacion) && !(($formato_cotizacion->tamanio_kb/1024)<$tamanio_mb)){
                $solicitud = SolicitudCompra::find($this->invitacion->transaccionAntecedente->id_transaccion);
                $pdf = new SolicitudCompraFormato($solicitud);
                return (new MailMessage)
                    ->subject("Solicitud de Cotización")
                    ->view('emails.invitacion_cotizar',["invitacion"=>$this->invitacion])
                    ->attach($path_carta_terminos,["as"=>$carta_terminos->tipo->descripcion.".".$carta_terminos->extension])
                    ->attachData($pdf->Output("S","solicitud_compra_".$this->invitacion->transaccionAntecedente->numero_folio.".pdf"), 'solicitud_compra_'.$this->invitacion->transaccionAntecedente->numero_folio.'.pdf',['mime' => 'application/pdf']);
            }
            //1111
            else if(file_exists($path_carta_terminos)  && (($carta_terminos->tamanio_kb/1024)<$tamanio_mb) && file_exists($path_formato_cotizacion) && (($formato_cotizacion->tamanio_kb/1024)<$tamanio_mb)){
                $solicitud = SolicitudCompra::find($this->invitacion->transaccionAntecedente->id_transaccion);
                $pdf = new SolicitudCompraFormato($solicitud);
                return (new MailMessage)
                    ->subject("Solicitud de Cotización")
                    ->view('emails.invitacion_cotizar',["invitacion"=>$this->invitacion])
                    ->attach($path_carta_terminos,["as"=>$carta_terminos->tipo->descripcion.".".$carta_terminos->extension])
                    ->attach($path_formato_cotizacion,["as"=>$formato_cotizacion->tipo->descripcion.".".$formato_cotizacion->extension])
                    ->attachData($pdf->Output("S","solicitud_compra_".$this->invitacion->transaccionAntecedente->numero_folio.".pdf"), 'solicitud_compra_'.$this->invitacion->transaccionAntecedente->numero_folio.'.pdf',['mime' => 'application/pdf']);
            }
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
