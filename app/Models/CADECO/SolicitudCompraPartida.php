<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 05/11/2019
 * Time: 04:56 p. m.
 */


namespace App\Models\CADECO;


use App\Models\CADECO\Compras\SolicitudPartidaComplemento;

class SolicitudCompraPartida extends Item
{
    protected $fillable = [
        'id_item',
        'id_transaccion',
        'id_material',
        'unidad',
        'cantidad',
        'id_concepto',
        'id_almacen'
    ];

    public function complemento()
    {
        return $this->belongsTo(SolicitudPartidaComplemento::class, 'id_item', 'id_item');
    }

    public function entrega()
    {
        return $this->belongsTo(Entrega::class, 'id_item', 'id_item');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }

    public function inventario()
    {
        return $this->hasMany(Inventario::class, 'id_material', 'id_material');
    }

    public function itemsOrdenCompra()
    {
        return $this->hasMany(ItemOrdenCompra::class, 'item_antecedente', 'id_item');
    }    

    public function getOrdenCompraAttribute()
    {        
        return $this->join('transacciones', 'transacciones.id_transaccion', 'items.id_transaccion')
        ->where('tipo_transaccion', '=', 19)->where('opciones', '=', 1)
        ->where('items.id_material', '=', $this->id_material)->sum('cantidad');
    }

    public function getEntradaMaterialAttribute()
    {
        return $this->join('transacciones', 'transacciones.id_transaccion', 'items.id_transaccion')
        ->where('tipo_transaccion', '=', 33)->where('opciones', '=', 1)
        ->where('items.id_material', '=', $this->id_material)->sum('cantidad'); 
    }
}
