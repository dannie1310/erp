<?php


namespace App\Observers\CADECO;


use App\Models\CADECO\Inventario;
use App\Models\CADECO\NuevoLotePartida;

class NuevoLotePartidaObserver
{
    public function created(NuevoLotePartida $nuevoLotePartida)
    {
        Inventario::query()->create([
            'id_almacen' => $nuevoLotePartida->id_almacen,
            'id_material' => $nuevoLotePartida->id_material,
            'id_item' => $nuevoLotePartida->id_item,
            'cantidad' => $nuevoLotePartida->cantidad,
            'saldo' => $nuevoLotePartida->cantidad,
            'monto_total' => $nuevoLotePartida->importe,
            'monto_pagado' => $nuevoLotePartida->importe - $nuevoLotePartida->saldo,
        ]);
    }

}
