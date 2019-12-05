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

    public  function facturasPartida(){
        return $this->hasMany(FacturaPartida::class,  'item_antecedente', 'id_item');
    }

    public function material()
    {
        return $this->belongsTo(Material::class,  'id_material');
    }

    public function scopeDisponibleEntradaAlmacen($query)
    {
        return $query->whereHas('entrega', function ($qu) {
            return $qu->whereRaw('ROUND(cantidad, 2) - ROUND(surtida, 2) > 0');
        });
    }

    public function getPagadoAttribute()
    {
        $pagado = 0;
        if($this->facturasPartida)
        {
            $pagado = round($this->facturasPartida->sum("importe")-$this->facturasPartida->sum("saldo"),2);
        }
        return $pagado;
    }
    public function getAplicadoAttribute()
    {
        $aplicado = round(($this->importe*$this->anticipo/100)-$this->saldo,2);
        return $aplicado;
    }
    public function getPorAplicarAttribute()
    {
        return round($this->pagado - $this->aplicado,2);
    }
    public function disminuyeSaldo($monto)
    {
        $saldo = $this->saldo-$monto;
        /*Se realiza esta validación por el error existente en el registro de partida de ordenes de compra con anticipo
        cuyo saldo queda en 0*/
        if($saldo<-0.01)
        {
            $saldo = 0;
        }
        $this->saldo = $saldo;
        $this->save();
    }
}