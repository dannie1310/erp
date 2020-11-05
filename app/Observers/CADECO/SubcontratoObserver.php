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
use App\Models\CADECO\Transaccion;

class SubcontratoObserver extends TransaccionObserver
{
    /**
     * @param Subcontrato $subcontrato
     * @throws \Exception
     */
    public function creating(Transaccion $subcontrato)
    {
        parent::creating($subcontrato);
        $subcontrato->tipo_transaccion = 51;
        $subcontrato->opciones = 2;
        $subcontrato->fecha = date('Y-m-d');
    }

    public function created(Subcontrato $subcontrato)
    {
        if ($subcontrato->retencion > 0) {
            $subcontrato->generaFondoGarantia();
        }
    }
}