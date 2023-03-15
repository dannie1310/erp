<?php

namespace App\Listeners;


use App\Events\RegistroNotificacionREP;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Fiscal\ProveedorREP;
use App\Models\SEGURIDAD_ERP\Notificaciones\Suscripcion;
use App\Mail\NotificacionREP;
use App\PDF\Fiscal\Comunicado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class GuardaComunicadoNotificacionREP implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param RegistroNotificacionREP $event
     * @return void
     */
    public function handle(RegistroNotificacionREP $event)
    {
        $proveedor = ProveedorREP::find($event->notificacion->id_proveedor_sat);

        $uuids = $proveedor->cfdi()->repPendiente()->get();
        $arr_comunicados = [];
        foreach ($uuids as $uuid)
        {
            $arr_comunicados["proveedor"] = $proveedor->proveedor;
            $arr_comunicados["receptores"][$uuid->rfc_receptor]["empresa"] = $uuid->empresa->razon_social;
            $arr_comunicados["receptores"][$uuid->rfc_receptor]["uuid"][] = $uuid;
        }

        $dir_descarga = public_path("downloads/fiscal/comunicados_notificaciones/");
        if (!file_exists($dir_descarga) && !is_dir($dir_descarga)) {
            mkdir($dir_descarga, 777, true);
        }

        $pdf = new Comunicado($arr_comunicados);
        $pdf->Output("F", $dir_descarga.$event->notificacion->id.".pdf",1);

    }
}
