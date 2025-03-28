<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 05/11/2019
 * Time: 04:56 p. m.
 */


namespace App\Models\CADECO;


use App\Models\CADECO\Compras\SolicitudPartidaComplemento;
use App\Models\CADECO\Compras\AsignacionProveedorPartida;
use App\Models\CADECO\CotizacionCompraPartida;

class ItemSolicitudCompra extends Item
{
    protected $fillable = [
        'id_item',
        'id_transaccion',
        'id_material',
        'unidad',
        'cantidad'
    ];

    /**
     * Relaciones
     */
    public function complemento()
    {
        return $this->belongsTo(SolicitudPartidaComplemento::class, 'id_item', 'id_item');
    }

    public function entrega()
    {
        return $this->belongsTo(Entrega::class, 'id_item', 'id_item');
    }

    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'id_concepto');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }

    public function inventario()
    {
        return $this->hasMany(Inventario::class, 'id_material', 'id_material');
    }

    public function asignaciones()
    {
        return $this->hasMany(AsignacionProveedorPartida::class, 'id_item_solicitud', 'id_item');
    }

    public function itemsOrdenCompra()
    {
        return $this->hasMany(ItemOrdenCompra::class, 'item_antecedente', 'id_item');
    }

    /**
     * Scopes
     */
    public function scopeOrdenarPartidas($query)
    {
        return $query->orderBy('id_material', 'asc');
    }


    /**
     * Atributos
     */
    public function getCantidadOrdenCompraAttribute()
    {
        return $this->join('transacciones', 'transacciones.id_transaccion', 'items.id_transaccion')
        ->where('tipo_transaccion', '=', 19)->where('opciones', '=', 1)
        ->where('items.id_material', '=', $this->id_material)->sum('cantidad');
    }

    public function getCantidadOrdenCompraFormatAttribute()
    {
        return number_format($this->cantidad_orden_compra, 1,'.',',');
    }

    public function getCantidadEntradaMaterialAttribute()
    {
        return $this->join('transacciones', 'transacciones.id_transaccion', 'items.id_transaccion')
        ->where('tipo_transaccion', '=', 33)->where('opciones', '=', 1)
        ->where('items.id_material', '=', $this->id_material)->sum('cantidad');
    }

    public function getCantidadEntradaMaterialFormatAttribute()
    {
        return number_format($this->cantidad_entrada_material, 1,'.',',');
    }

    public function getSolicitadoCantidadFormatAttribute()
    {
        return number_format($this->cantidad, 1,'.',',');
    }

    public function getCantidadOriginalFormatAttribute()
    {
        return number_format($this->cantidad_original1, 1,'.',',');
    }

    public function getSumaInventarioFormatAttribute()
    {
        return number_format($this->inventario->sum('saldo'), 1,'.',',');
    }

    public function getCantidadFormatAttribute()
    {
        return number_format($this->cantidad, 1, '.', ',');
    }

    public function getCantidadDecimalAttribute()
    {
        return number_format($this->cantidad,2,'.', '');

    }

    /**
     * Métodos
     */
    public function estaPartidaCotizada($id_cotizacion, $id_material)
    {
        $partida = CotizacionCompraPartida::where('id_transaccion', $id_cotizacion)->where('id_material', $id_material)->activa()->first();
        if(!is_null($partida))
        {
            return true;
        }
        return false;
    }
}
