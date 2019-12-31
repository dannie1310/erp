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
use App\Models\CADECO\PagoFactura;
use App\Models\CADECO\Transaccion;

class PagoFacturaObserver extends PagoObserver
{
    /**
     * @param Pago $pago
     * @throws \Exception
     */
    public function creating(Transaccion $pago)
    {
        parent::creating($pago);
        $pago->opciones = 0;
    }

    public function created(Pago $pago){
        parent::created($pago);
        $pago->orden_pago->factura->disminuyeSaldo($pago);
        $pago->orden_pago->factura->contra_recibo->disminuyeSaldo($pago);
    }
}
