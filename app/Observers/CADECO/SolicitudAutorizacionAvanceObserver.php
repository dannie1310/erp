<?php


namespace App\Observers\CADECO;


use App\Models\CADECO\SolicitudAutorizacionAvance;
use App\Models\CADECO\Transaccion;

class SolicitudAutorizacionAvanceObserver extends TransaccionObserver
{
    /**
     * @param SolicitudAutorizacionAvance $solicitud
     *  @throws \Exception
     */
    public function creating(Transaccion $solicitud)
    {
        parent::creating($solicitud);
        $solicitud->tipo_transaccion = 55;
        $solicitud->saldo = $solicitud->monto;
        $solicitud->fecha = $solicitud->fecha;
    }

    public function created(SolicitudAutorizacionAvance $solicitud)
    {
        if ($solicitud->retencion > 0) {
            $fondo_garantia = $solicitud->fondoGarantia;
            if (is_null($fondo_garantia)) {
                $solicitud->generaFondoGarantia();
            }
        }
        /**
         *  Se quita la creacion del subcontrato estimación pues aun no se define
         */
        //$solicitud->creaSubcontratoEstimacion();
    }

    public function updating(SolicitudAutorizacionAvance $solicitud)
    {
        if(($solicitud->getOriginal('estado') == $solicitud->estado) && $solicitud->estado > 0)
        {
            abort(400, "Esta estimación no puede ser editada se encuentra con estado ".$solicitud->estado_descripcion.".");
        }
    }

    public function deleting(SolicitudAutorizacionAvance $solicitud)
    {
        $solicitud->desvincularPolizas();
        $solicitud->validarParaEliminar();
        if($solicitud->solicitudEliminada == null)
        {
            abort(400, "Error al eliminar, respaldo incorrecto.");
        }

        if($solicitud->retencion_fondo_garantia)
        {
            $solicitud->retencion_fondo_garantia->eliminaEstimacion();
        }
    }
}
