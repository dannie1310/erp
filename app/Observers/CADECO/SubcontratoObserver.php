<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 04:59 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Subcontrato;

class SubcontratoObserver
{
    /**
     * @param Subcontrato $subcontrato
     * @throws \Exception
     */
    public function creating(Subcontrato $subcontrato)
    {
        if (!$subcontrato->validaTipoAntecedente()) {
            throw New \Exception('La transacción antecedente no es válida');
        }
        $subcontrato->tipo_transaccion = 51;
        $subcontrato->opciones = 2;
        $subcontrato->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
        $subcontrato->FechaHoraRegistro = date('Y-m-d h:i:s');
        $subcontrato->id_obra = Context::getIdObra();
    }

    public function created(Subcontrato $subcontrato)
    {
        if ($subcontrato->retencion > 0) {
            $subcontrato->generaFondoGarantia();
        }
    }
}