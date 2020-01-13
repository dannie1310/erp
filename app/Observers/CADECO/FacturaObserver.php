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

class FacturaObserver extends TransaccionObserver
{
    /**
     * @param Pago $pago
     * @throws \Exception
     */
    public function creating(Transaccion $factura)
    {
        parent::creating($factura);
        $factura->tipo_transaccion = 65;
        $factura->opciones = 0;
    }

    public function updating(Factura $factura){
        if($factura->saldo<-0.1)
        {
            throw New \Exception('El saldo de la factura '.$factura->referencia.' no puede ser menor a 0');
        }
    }
}
