<?php


namespace App\Observers\SEGURIDAD_ERP\Finanzas;



use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDI;

class SolicitudRecepcionCFDIObserver
{
    /**
     * @param SolicitudRecepcionCFDI $solicitud
     */
    public function creating(SolicitudRecepcionCFDI $solicitud)
    {
        $solicitud->fecha_hora_registro = date('Y-m-d H:i:s');
        $solicitud->usuario_registro = auth()->id();
        $solicitud->numero_folio = SolicitudRecepcionCFDI::calcularFolio($solicitud->id_empresa_emisora);
    }

    public function updating(SolicitudRecepcionCFDI $solicitud)
    {
        if($solicitud->estado == 1){
            $solicitud->fecha_hora_aprobacion = date('Y-m-d H:i:s');
            $solicitud->usuario_aprobo = auth()->id();
        }
        if($solicitud->estado == -2){
            $solicitud->fecha_hora_rechazo = date('Y-m-d H:i:s');
            $solicitud->usuario_rechazo = auth()->id();
        }
    }
}
