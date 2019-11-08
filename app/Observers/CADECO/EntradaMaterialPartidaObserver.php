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
        if($partida->id_almacen != null) {
            Inventario::query()->create([
                'id_almacen' => $partida->id_almacen,
                'id_material' => $partida->id_material,
                'id_item' => $partida->id_item,
                'cantidad' => $partida->cantidad,
                'saldo' => $partida->cantidad,
                'monto_total' => $partida->importe,
                'monto_pagado' => $partida->importe - $partida->saldo,
            ]);
        }
    }
}