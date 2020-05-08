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
                'id_material' => $partida->,
                'numero',
                'disponibles',
                'cantidad',
                'precio_unitario',
                'descuento',
                'descuento_adicional',
                'otros_descuentos',
                'flete',
                'anticipo',
                'dias_credito',
                'dias_entrega',
                'no_cotizado',
                'id_moneda',
                'capacidad',
                'descuento_partida',
                'observaciones',
                'estatus',
                'timestamp_registro',
                'usuario_elimina' => auth()->id(),
                'fecha_eliminacion' => date('Y-m-d H:i:s')

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
        if($partida->complemento)
        {
            $partida->complemento->delete();
        }
}
