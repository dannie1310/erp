<?php
/**
 * Created by PhpStorm.
 * User: Emartinez
 * Date: 19/11/2019
 * Time: 06:34 PM
 */

namespace App\Observers\CADECO;

use App\Models\CADECO\Compras\OrdenCompraEliminada;
use App\Models\CADECO\Compras\OrdenCompraPartidaEliminada;
use App\Models\CADECO\OrdenCompraPartida;
use App\Models\CADECO\Compras\CotizacionComplementoPartida;

class OrdenCompraPartidaObserver
{
    public function created(OrdenCompraPartida $partida){
        $cotizacion_partida_comp = CotizacionComplementoPartida::where('id_transaccion', '=', $partida->ordenCompra->id_referente)->where('id_material', '=', $partida->id_material)->first();
        $partida->orden_partida_complemento()->create([
            'id_item' => $partida->id_item,
            'descuento' =>$cotizacion_partida_comp? $cotizacion_partida_comp->descuento_partida:0,
            'id_moneda' => $partida->ordenCompra->id_moneda,
            'observaciones' => ' ',
        ]);
    }

    public function updating(OrdenCompraPartida $partida)
    {
        if($partida->saldo<-0.01)
        {
            throw New \Exception('El saldo de la partida de la orden de compra de material'.$partida->material->descripcion.' no puede ser menor a 0, por favor solicite una revisión al área de soporte a aplicaciones');
        }
    }

    public function deleting(OrdenCompraPartida $partida)
    {
        $ordenEliminada=OrdenCompraEliminada::where('id_transaccion', $partida->id_transaccion)->first();
        OrdenCompraPartidaEliminada::create([
            'id_orden_compra_eliminada' => $ordenEliminada->id,
            'id_item' => $partida->id_item,
            'id_transaccion' => $partida->id_transaccion,
            'id_antecedente' => $partida->id_antecedente,
            'id_material' => $partida->id_material,
            'unidad' => $partida->unidad,
            'cantidad' => $partida->cantidad,
            'anticipo' => $partida->anticipo,
            'descuento' => $partida->descuento,
            'precio_material' => $partida->precio_material,
            'item_antecedente' => $partida->item_antecedente,
            'precio_unitario' => $partida->precio_unitario,
            'importe' => $partida->importe,
            'saldo' => $partida->saldo,
            'elimino' => auth()->id(),
            'id_moneda' => $partida->id_moneda,
        ]);

        if($partida->complemento)
        {
            $partida->complemento->delete();
        }
    }
}
