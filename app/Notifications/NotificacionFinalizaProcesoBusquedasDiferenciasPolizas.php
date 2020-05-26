<?php

namespace App\Notifications;

use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\LoteBusqueda;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotificacionFinalizaProcesoBusquedasDiferenciasPolizas extends Notification
{
    use Queueable;
    public $lote;
    public $diferencias_totales;
    public $cantidad_diferencias_totales;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(LoteBusqueda $lote, $diferencias_totales, $cantidad_diferencias_totales)
    {
        $this->lote = $lote;
        $this->diferencias_totales = $diferencias_totales;
        $this->cantidad_diferencias_totales = $cantidad_diferencias_totales;
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

        return (new MailMessage)
            ->subject("Finalización de Proceso de Búsqueda de Diferencias en Pólizas")
            ->view('emails.lote_busqueda_diferencia_polizas',["lote"=>$this->lote, "diferencias_totales"=>$this->diferencias_totales, "cantidad_diferencias_totales" => $this->cantidad_diferencias_totales]);
            /*->attach($this->xml);*/
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
