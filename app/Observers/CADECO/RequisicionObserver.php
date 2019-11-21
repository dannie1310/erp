<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/11/2019
 * Time: 05:33 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\Requisicion;
use App\Models\CADECO\Transaccion;

class RequisicionObserver extends TransaccionObserver
{
    /**
     * @param Requisicion $requisicion
     */
    public function creating(Transaccion $requisicion)
    {
        parent::creating($requisicion);
        $requisicion->tipo_transaccion = 16;
        $requisicion->estado = 0;
        $requisicion->opciones = 1;
    }
}