<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 04:46 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Pago;
use App\Models\CADECO\Transaccion;

class PagoObserver extends TransaccionObserver
{
    /**
     * @param Pago $pago
     * @throws \Exception
     */
    public function creating(Transaccion $pago)
    {
        parent::creating($pago);
         if (!$pago->validaTipoAntecedente()) {
                throw New \Exception('La transacción antecedente no es válida');
         }
        $pago->tipo_transaccion = 82;
        $pago->opciones = 0;
    }
}
