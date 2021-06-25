<?php


namespace App\Notifications;


use App\Informes\Fiscal\NoLocalizadosInforme;
use App\Models\SEGURIDAD_ERP\Contabilidad\CargaCFDSAT;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgNoLocalizado;
use App\PDF\Fiscal\InformeNoLocalizadosEmpresaProyecto;
use App\Repositories\SEGURIDAD_ERP\Fiscal\CtgNoLocalizadoRepository as Repository;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotificacionCambioNoLocalizados extends Notification
{
    use Queueable;
    public $cambios;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($altas, $bajas)
    {
        $this->altas = $altas;
        $this->bajas = $bajas;
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

        $informe = NoLocalizadosInforme::getInformeEmpresaProyecto();
        $pdf = null;
        if(count($informe["informe"][0])>0){
            $pdf = new InformeNoLocalizadosEmpresaProyecto($informe);
        }

        if($pdf){
            return (new MailMessage)
                ->subject("Cambios en proveedores no localizados")
                ->view('emails.cambios_no_localizados',["altas"=>$this->altas, "bajas"=>$this->bajas])
                ->attachData($pdf->Output("S","informeNoLocalizados.pdf"), 'informeNoLocalizados.pdf',['mime' => 'application/pdf']);
        } else {
            return (new MailMessage)
                ->subject("Cambios en proveedores no localizados")
                ->view('emails.cambios_no_localizados',["altas"=>$this->altas, "bajas"=>$this->bajas]);
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
