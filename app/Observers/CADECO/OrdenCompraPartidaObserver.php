<?php
/**
 * Created by PhpStorm.
 * User: Emartinez
 * Date: 19/11/2019
 * Time: 06:34 PM
 */

namespace App\Observers\CADECO;

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
}