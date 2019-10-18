<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 04:48 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\PagoACuenta;
use App\Models\CADECO\Transaccion;

class PagoACuentaObserver extends TransaccionObserver
{
    /**
     * @param PagoACuenta $pagoACuenta
     *  @throws \Exception
     */
    public function creating(Transaccion $pagoACuenta)
    {
        parent::creating($pagoACuenta);
        $pagoACuenta->tipo_transaccion = 82;
        $pagoACuenta->opciones = 327681;
    }
}
