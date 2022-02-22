<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/01/2020
 * Time: 05:06 PM
 */

namespace App\Models\CADECO;


class ItemPrestacion extends Item
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereHas('prestacion');
        });
    }

    public function inventario()
    {
        return $this->hasOne(Inventario::class, 'id_item', 'item_antecedente');
    }

    public function prestacion()
    {
        return $this->belongsTo(Prestacion::class, 'id_transaccion', 'id_transaccion');
    }


}
