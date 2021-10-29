<?php


namespace App\Observers\CADECO;


use App\Models\CADECO\AvanceObra\AvanceObraPartidaEliminada;
use App\Models\CADECO\ItemAvanceObra;

class AvanceObraPartidaObserver
{
    public function deleting(ItemAvanceObra $item)
    {
        AvanceObraPartidaEliminada::create([
            'id_item' => $item->id_item,
            'id_transaccion' => $item->id_transaccion,
            'id_concepto' => $item->id_concepto,
            'numero' => $item->numero,
            'cantidad' => $item->cantidad,
            'importe' => $item->importe,
            'precio_unitario' => $item->precio_unitario,
            'estado' => $item->estado
        ]);
    }
}
