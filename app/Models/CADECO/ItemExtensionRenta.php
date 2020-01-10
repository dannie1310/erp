<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/01/2020
 * Time: 09:46 PM
 */

namespace App\Models\CADECO;


class ItemExtensionRenta extends Item
{
    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'id_item', 'id_item');
    }
}