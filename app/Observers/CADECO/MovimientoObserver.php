<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 05/11/2019
 * Time: 10:12 AM
 */

namespace App\Observers\CADECO;

use App\Models\CADECO\Movimiento;
use App\Models\CADECO\Compras\MovimientoEliminado;


class MovimientoObserver
{
    public function deleting(Movimiento $movimiento)
    {
        if($movimiento->inventario){
            $movimiento->inventario->aumentaSaldo($movimiento->cantidad);
        }
        MovimientoEliminado::create(
            [
                'id_movimiento' => $movimiento->id_movimiento,
                'id_concepto' => $movimiento->id_concepto,
                'id_item' => $movimiento->id_item,
                'id_material' => $movimiento->id_material,
                'cantidad' => $movimiento->cantidad,
                'monto_total' => $movimiento->monto_total,
                'monto_pagado' => $movimiento->monto_pagado,
                'monto_original' => $movimiento->monto_original,
                'creado' => $movimiento->creado
            ]
        );


    }
}