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
use App\Models\CADECO\Pago;

class PagoACuentaObserver extends PagoObserver
{
    /**
     * @param PagoACuenta $pagoACuenta
     * @throws \Exception
     */
    public function creating(Transaccion $pago)
    {
        parent::creating($pago);
        $pago->tipo_transaccion = 82;
        $pago->opciones = 327681;
    }

    public function created(Pago $pago)    {
        parent::created($pago);
        $pago->solicitud->actualizaEstadoPagada();
    }
}
