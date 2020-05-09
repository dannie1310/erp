<?php


namespace App\Observers\CADECO;


use App\Models\CADECO\Compras\CotizacionPartidaEliminada;
use App\Models\CADECO\CotizacionCompraPartida;

class CotizacionCompraPartidaObserver
{
    public function deleting(CotizacionCompraPartida $partida)
    {
        CotizacionPartidaEliminada::create(
            [
                'id_transaccion' => $partida->id_transaccion,
                'id_material' => $partida->id_material,
                'numero' => $partida->numero,
                'disponibles' => $partida->disponibles,
                'cantidad' => $partida->cantidad,
                'precio_unitario' => $partida->precio_unitario,
                'descuento' => $partida->descuento,
                'descuento_adicional' => $partida->descuento_adicional,
                'otros_descuentos' => $partida->otros_descuentos,
                'flete' => $partida->flete,
                'anticipo' => $partida->anticipo,
                'dias_credito' => $partida->dias_credito,
                'dias_entrega' => $partida->dias_entrega,
                'no_cotizado' => $partida->no_cotizado,
                'id_moneda' => $partida->id_moneda,
                'capacidad' => $partida->capacidad,
                'descuento_partida' => $partida->partida ? $partida->partida->descuento_partida : NULL,
                'observaciones' => $partida->partida ? $partida->partida->observaciones : NULL,
                'estatus' => $partida->partida ? $partida->partida->estatus : NULL,
                'timestamp_registro' => $partida->partida ? $partida->partida->timestamp_registro : NULL,
                'usuario_elimina' => auth()->id(),
                'fecha_eliminacion' => date('Y-m-d H:i:s')
            ]
        );

        if ($partida->partida)
        {
            $partida->partida->delete();
        }
    }
}
