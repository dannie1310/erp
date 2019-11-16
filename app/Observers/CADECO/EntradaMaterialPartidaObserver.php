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

class EntradaMaterialPartidaObserver
{
    /**
     * @param EntradaMaterialPartida $partida
     */
    public function creating(EntradaMaterialPartida $partida)
    {
        $partida->estado = 0;
    }

    public function created(EntradaMaterialPartida $partida)
    {
        $pagado = round($partida->anticipo * $partida->precio_unitario * $partida->cantidad_original1 /100,2);
        if($partida->id_almacen != null) {
            Inventario::create([
                'id_almacen' => $partida->id_almacen,
                'id_material' => $partida->id_material,
                'id_item' => $partida->id_item,
                'cantidad' => $partida->cantidad,
                'saldo' => $partida->cantidad,
                'monto_total' => $partida->importe,
                'monto_pagado' => $pagado,
            ]);
        }
        if($partida->id_concepto != null) {
            Movimiento::create([
                'id_concepto' => $partida->id_concepto,
                'id_item' => $partida->id_item,
                'id_material' => $partida->id_material,
                'cantidad' => $partida->cantidad,
                'monto_total' => $partida->importe,
                'monto_original' => $partida->importe,
                'monto_pagado' => $pagado,
            ]);
        }
        $partida->ordenCompraPartida->entrega->surte($partida->cantidad);
    }
}