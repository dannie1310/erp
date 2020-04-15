<?php


namespace App\Observers\CADECO;


use App\Models\CADECO\Compras\EntregaEliminada;
use App\Models\CADECO\Compras\SolicitudPartidaEliminada;
use App\Models\CADECO\ItemSolicitudCompra;

class SolicitudCompraPartidaObserver
{
    public function deleting(ItemSolicitudCompra $partida)
    {
        SolicitudPartidaEliminada::create(
            [
                'id_item' => $partida->id_item,
                'id_transaccion' => $partida->id_transaccion,
                'id_material' => $partida->id_material,
                'unidad' => $partida->unidad,
                'cantidad' => $partida->cantidad,
                'estado' => $partida->estado,
                'cantidad_original1' => $partida->cantidad_original1,
                'fecha_entrega' => $partida->complemento ? $partida->complemento->fecha_entrega : NULL,
                'observaciones' => $partida->complemento ? $partida->complemento->observaciones : NULL
            ]
        );

        EntregaEliminada::create(
            [
                'id_item' => $partida->id_item,
                'numero_entrega' => $partida->entrega->numero_entrega,
                'fecha' => $partida->entrega->fecha,
                'cantidad' => $partida->entrega->cantidad,
                'surtida' => $partida->entrega->surtida,
                'pedidas' => $partida->entrega->pedidas,
                'asignadas' => $partida->entrega->asignadas,
                'id_concepto' => $partida->entrega->id_concepto,
                'id_almacen' => $partida->entrega->id_almacen
            ]
        );
        $partida->entrega->delete();
        $partida->complemento->delete();
    }
}
