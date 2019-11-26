<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 05/11/2019
 * Time: 10:12 AM
 */

namespace App\Observers\CADECO;

use App\Models\CADECO\Movimiento;


class MovimientoObserver
{
    /**
     * @param Inventario $inventario
     * @throws \Exception
     */
    public function updating(Movimiento $inventario)
    {
        if($inventario->saldo<-0.01){
            throw New \Exception('El saldo del lote ('.$inventario->id_lote.') '.$inventario->material->descripcion.' no puede ser menor a 0');
        }
        if($inventario->saldo > ($inventario->cantidad)+0.01){
            throw New \Exception('El saldo del lote ('.$inventario->id_lote.') '.$inventario->material->descripcion.' no puede ser mayor a '. $inventario->cantidad);
        }
    }

    public function deleting(Movimiento $movimiento)
    {
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