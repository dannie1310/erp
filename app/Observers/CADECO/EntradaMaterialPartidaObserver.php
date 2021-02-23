<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 11/10/2019
 * Time: 06:34 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\EntradaMaterialPartida;
use App\Models\CADECO\Inventario;
use App\Models\CADECO\Movimiento;
use App\Models\CADECO\Compras\ItemEntradaEliminada;

class EntradaMaterialPartidaObserver
{
    /**
     * @param EntradaMaterialPartida $partida
     * @throws \Exception
     */
    public function creating(EntradaMaterialPartida $partida)
    {
        $partida->estado = 0;
        /*Amoritzación de anticipo, esta lógica estaba incluida en el stored procedure: sp_entrada_material*/
        $pagado = $partida->pagado_registro;
        if ($pagado > 0) {
            $partida->saldo = $partida->saldo - $pagado;
        }
        if ($partida->saldo < -0.01) {
            throw New \Exception('El saldo de la partida de entrada ' . $partida->material->descripcion . ' no puede ser menor a 0');
        }
        if ($partida->saldo < 0.01) {
            $partida->estado = 1;
        }
    }

    /**
     * @param EntradaMaterialPartida $partida
     */
    public function created(EntradaMaterialPartida $partida)
    {
        $factor = $partida->entrada->factor_conversion;
        $pagado = $partida->pagado_registro;
        if ($partida->id_almacen != null) {
            Inventario::create([
                'id_almacen' => $partida->id_almacen,
                'id_material' => $partida->id_material,
                'id_item' => $partida->id_item,
                'cantidad' => $partida->cantidad,
                'saldo' => $partida->cantidad,
                'monto_total' => round($partida->importe * $factor, 2),
                'monto_original' => round($partida->importe * $factor, 2),
                'monto_pagado' => round($pagado * $factor, 2),
                'monto_anticipo' => 0
            ]);
            $partida->ajustarValoresConsumos();
        }
        if ($partida->id_concepto != null) {
            Movimiento::create([
                'id_concepto' => $partida->id_concepto,
                'id_item' => $partida->id_item,
                'id_material' => $partida->id_material,
                'cantidad' => $partida->cantidad,
                'monto_total' => round($partida->importe * $factor, 2),
                'monto_original' => round($partida->importe * $factor, 2),
                'monto_pagado' => round($pagado * $factor, 2),
            ]);
        }
        $partida->ordenCompraPartida->entrega->surte($partida->cantidad);
        if ($pagado > 0) {
            /*Amoritzación de anticipo, esta lógica estaba incluida en el stored procedure: sp_entrada_material*/
            $partida->ordenCompraPartida->disminuyeSaldo($pagado);
        }
    }

    public function deleting(EntradaMaterialPartida $partida)
    {
        if ($partida->inventario)
        {
            $partida->inventario->delete();
        }
        else if ($partida->movimiento) {
            $partida->movimiento->delete();
        }
    }

    public function deleted(EntradaMaterialPartida $partida)
    {
        ItemEntradaEliminada::create(
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
        /*
         * Recalcular saldo de partidas de OC, lógica incluida en el procedimiento almacenado:
         * sp_borra_transaccion y que se manda a llamar al eliminar una entrada de almacén
         * */
        $importe_anticipo = $partida->importe_anticipo;
        $pagado_oc = $partida->ordenCompraPartida->pagado;
        $partida->ordenCompraPartida->entrega->recalculaSurtido();
        if ($partida->ordenCompraPartida->anticipo > 0) {
            $proporcion_pagada = round($importe_anticipo * $pagado_oc / ($partida->ordenCompraPartida->importe * $partida->ordenCompraPartida->anticipo / 100), 2);
            if ($proporcion_pagada > 0) {
                $partida->ordenCompraPartida->aumentaSaldo($proporcion_pagada);
            }
        }
    }
}
