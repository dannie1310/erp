<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 02:35 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Estimacion;
use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\Transaccion;

class EstimacionObserver extends TransaccionObserver
{
    /**
     * @param Estimacion $estimacion
     *  @throws \Exception
     */
    public function creating(Transaccion $estimacion)
    {
        parent::creating($estimacion);
        $subcontrato = Subcontrato::find($estimacion->id_antecedente);
        $estimacion->tipo_transaccion = 52;
        $estimacion->id_empresa = $subcontrato->id_empresa;
        $estimacion->id_moneda = $subcontrato->id_moneda;
        $estimacion->saldo = $estimacion->monto;
        $estimacion->retencion = $subcontrato->retencion;
        $estimacion->anticipo = $subcontrato->anticipo;
        $estimacion->fecha = $estimacion->fecha;
        $estimacion->numero_folio = $estimacion->calcularFolio();
    }

    public function created(Estimacion $estimacion)
    {
        if ($estimacion->retencion > 0)
        {
            $fondo_garantia = $estimacion->subcontrato->fondo_garantia;
            if(is_null($fondo_garantia))
            {
                $estimacion->subcontrato->generaFondoGarantia();
            }
        }
        $estimacion->creaSubcontratoEstimacion();
    }

    public function updating(Estimacion $estimacion)
    {
        if(($estimacion->getOriginal('estado') == $estimacion->estado) && $estimacion->estado > 0)
        {
            abort(400, "Esta estimación no puede ser editada se encuentra con estado ".$estimacion->estado_descripcion.".");
        }
    }

    public function deleting(Estimacion $estimacion)
    {
        $estimacion->validarParaEliminar();
        if($estimacion->estimacionEliminada == null)
        {
            abort(400, "Error al eliminar, respaldo incorrecto.");
        }

        if($estimacion->retencion_fondo_garantia)
        {
            $estimacion->retencion_fondo_garantia->eliminaEstimacion();
        }
    }

    public function deleted(Estimacion $estimacion)
    {
        $estimacion->subcontratoEstimacion()->delete();
        $estimacion->subcontrato->cambioEstadoEliminarEstimacion();
    }
}
