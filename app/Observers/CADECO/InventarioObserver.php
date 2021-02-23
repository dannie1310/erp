<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 05/11/2019
 * Time: 10:12 AM
 */

namespace App\Observers\CADECO;

use App\Models\CADECO\Inventario;
use App\Models\CADECO\Compras\InventarioEliminado;

class InventarioObserver
{
    /**
     * @param Inventario $inventario
     * @throws \Exception
     */
    public function updating(Inventario $inventario)
    {
        if(round($inventario->getOriginal("saldo"),2) <> round($inventario->saldo,2) &&  $inventario->saldo<-0.01){
            throw New \Exception('El saldo del lote ('.$inventario->id_lote.') '.$inventario->material->descripcion.' no puede ser menor a 0');
        }
        if(round($inventario->getOriginal("saldo"),2) <> round($inventario->saldo,2) && $inventario->saldo > ($inventario->cantidad)+0.01){
            throw New \Exception('El saldo del lote ('.$inventario->id_lote.') '.$inventario->material->descripcion.' no puede ser mayor a '. $inventario->cantidad);
        }
    }

    public function deleting(Inventario $inventario)
    {
        if($inventario->inventario)
        {
            /* Este método implementa la lógica del procedimiento almacenado
             * sp_borra_transaccion y se detona al eliminar una salida de almacén (transferenncia)
             * */
            $inventario->inventario->aumentaSaldo($inventario->cantidad);
            $inventario->inventario->disminuyeMontoAplicado($inventario->monto_pagado);
        }
        InventarioEliminado::create(
            [
                'id_lote' => $inventario->id_lote,
                'lote_antecedente' => $inventario->lote_antecedente,
                'id_almacen' => $inventario->id_almacen,
                'id_material' => $inventario->id_material,
                'id_item' => $inventario->id_item,
                'saldo' => $inventario->saldo,
                'monto_total' => $inventario->monto_total,
                'monto_pagado' => $inventario->monto_pagado,
                'monto_aplicado' => $inventario->monto_aplicado,
                'fecha_desde' => $inventario->fecha_desde,
                'referencia' => $inventario->referencia,
                'monto_original' => $inventario->monto_original
            ]
        );
    }
}
