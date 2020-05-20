<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 02:46 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\OrdenCompra;
use App\Models\CADECO\Transaccion;

class OrdenCompraObserver extends TransaccionObserver
{
    /**
     * @param OrdenCompra $ordenCompra
     */
    public function creating(Transaccion $ordenCompra)
    {
       parent::creating($ordenCompra);
        $ordenCompra->fecha = date('Y-m-d');
        $ordenCompra->tipo_transaccion = 19;
        $ordenCompra->opciones = 1;
        
    }
}