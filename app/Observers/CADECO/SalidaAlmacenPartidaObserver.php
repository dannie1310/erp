<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 11/10/2019
 * Time: 06:34 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\SalidaAlmacenPartida;
use App\Models\CADECO\Compras\ItemSalidaEliminada;

class SalidaAlmacenPartidaObserver
{
    public function created(SalidaAlmacenPartida $partida)
    {
        /**
         * Se implementa la logica del stored procedure sp_salida_material
         * */
        $partida->salidaMaterial();

        /**
         * Se implementa la logica del stored procedure sp_entradas_salidas
         * */
        if ($partida->id_almacen != null) {
            $partida->ajustarValoresConsumos();
        }
    }

    public function deleting(SalidaAlmacenPartida $partida)
    {
        if(count($partida->inventario) > 0)
        {
            foreach ($partida->inventario as $inv)
            {
                $inv->delete();
            }
        }
        else if(count($partida->movimiento) > 0)
        {
            foreach ($partida->movimiento as $mov)
            {
                $mov->delete();
            }
        }
    }

    public function deleted(SalidaAlmacenPartida $partida)
    {
        ItemSalidaEliminada::create(
            [
                'id_item' => $partida->id_item,
                'id_transaccion' => $partida->id_transaccion,
                'id_antecedente' => $partida->id_antecedente,
                'item_antecedente' => $partida->item_antecedente,
                'id_almacen' => $partida->id_almacen,
                'id_concepto' => $partida->id_concepto,
                'id_material' => $partida->id_material,
                'unidad' => $partida->unidad,
                'numero' => $partida->numero,
                'cantidad' => $partida->cantidad,
                'cantidad_material' => $partida->cantidad_material,
                'importe' => $partida->importe,
                'saldo' => $partida->saldo,
                'precio_unitario' => $partida->precio_unitario,
                'anticipo' => $partida->anticipo,
                'precio_material' => $partida->precio_material,
                'referencia' => $partida->referencia,
                'estado' => $partida->estado,
                'cantidad_original1' => $partida->cantidad_original1,
                'precio_original1' => $partida->precio_original1,
                'id_asignacion' => $partida->id_asignacion
            ]
        );
    }
}
