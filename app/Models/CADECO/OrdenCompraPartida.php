<?php
/**
 * Created by PhpStorm.
 * User: Luis M.Valencia
 * Date: 01/07/2018
 * Time: 11:29 AM
 */

namespace App\Models\CADECO;

use App\Models\CADECO\Compras\OrdenCompraPartidaComplemento;
use App\Models\CADECO\Item;


class OrdenCompraPartida extends Item
{
    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'id_transaccion','id_transaccion');
    }

    public  function orden_partida_complemento(){
        return $this->hasOne(OrdenCompraPartidaComplemento::class, 'id_item');
    }

    public function entrega()
    {
        return $this->hasOne(Entrega::class, 'id_item');
    }

    public function entradas()
    {
        return $this->hasMany(EntradaMaterialPartida::class, 'item_antecedente', 'id_item');
    }

    public function scopeDisponibleEntradaAlmacen($query)
    {
        return $query->whereHas('entrega', function ($qu) {
            return $qu->whereRaw('ROUND(cantidad, 2) - ROUND(surtida, 2) > 0');
        });
    }

}