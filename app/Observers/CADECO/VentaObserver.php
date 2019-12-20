<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 16/12/2019
 * Time: 07:53 PM
 */

namespace App\Observers\CADECO;

use App\Models\CADECO\Transaccion;
use App\Models\CADECO\Venta;


class VentaObserver extends TransaccionObserver
{
    /**
     * @param Venta $venta
     * @throws \Exception
     */
    public function creating(Transaccion $venta)
    {
        parent::creating($venta);
        $venta->tipo_transaccion = 38;
        $venta->opciones = 1;
    }

    public function created(Venta $venta)
    {

    }
}