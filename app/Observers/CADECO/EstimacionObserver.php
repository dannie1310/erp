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

class EstimacionObserver
{
    /**
     * @param Estimacion $estimacion
     */
    public function creating(Estimacion $estimacion)
    {
        if (!$estimacion->validaTipoAntecedente()) {
            throw New \Exception('La transacción antecedente no es válida');
        }
        $subcontrato = Subcontrato::query()->find($estimacion->id_antecedente);

        $estimacion->tipo_transaccion = 52;
        $estimacion->id_empresa = $subcontrato->id_empresa;
        $estimacion->id_moneda = $subcontrato->id_moneda;
        $estimacion->saldo = $estimacion->monto;
        $estimacion->retencion = $subcontrato->retencion;
        $estimacion->fecha = date('Y-m-d');
        $estimacion->numero_folio = $estimacion->calcularFolio();
        $estimacion->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
        $estimacion->FechaHoraRegistro = date('Y-m-d h:i:s');
        $estimacion->id_obra = Context::getIdObra();
    }

    public function created(Estimacion $estimacion)
    {
        if ($estimacion->retencion > 0) {
            $estimacion->generaRetencion();
        }
        $estimacion->creaSubcontratoEstimacion();
    }
}