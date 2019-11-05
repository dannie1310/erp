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
        $subcontrato = Subcontrato::query()->find($estimacion->id_antecedente);
        $estimacion->tipo_transaccion = 52;
        $estimacion->id_empresa = $subcontrato->id_empresa;
        $estimacion->id_moneda = $subcontrato->id_moneda;
        $estimacion->saldo = $estimacion->monto;
        $estimacion->retencion = $subcontrato->retencion;
        $estimacion->fecha = date('Y-m-d');
        $estimacion->numero_folio = $estimacion->calcularFolio();
    }

    public function created(Estimacion $estimacion)
    {
        if ($estimacion->retencion > 0) {
            $estimacion->generaRetencion();
        }
        $estimacion->creaSubcontratoEstimacion();
    }

    public function deleting(Estimacion $estimacion)
    {
        $estimacion->validarParaEliminar();
        if($estimacion->estimacionEliminada == null)
        {
            abort(400, "Error al eliminar, respaldo incorrecto.");
        }
    }

    public function deleted(Estimacion $estimacion)
    {
        $estimacion->subcontratoEstimacion()->delete();
        $estimacion->subcontrato->cambioEstadoEliminarEstimacion();
    }
}