<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 04:46 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Factura;
use App\Models\CADECO\Pago;
use App\Models\CADECO\PagoFactura;
use App\Models\CADECO\Transaccion;
use App\Models\CADECO\Finanzas\FacturaEliminada;

class NotaCreditoObserver extends TransaccionObserver
{
    /**
     * @param Pago $pago
     * @throws \Exception
     */
    public function creating(Transaccion $factura)
    {
        parent::creating($factura);
        $factura->tipo_transaccion = 69;
        $factura->opciones = 0;
    }

}
