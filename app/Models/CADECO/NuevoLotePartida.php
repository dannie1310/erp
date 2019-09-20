<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/09/2019
 * Time: 01:21 PM
 */

namespace App\Models\CADECO;


class NuevoLotePartida extends Item
{

    protected $fillable = [
        'id_item',
        'id_almacen',
        'id_material',
        'unidad',
        'cantidad',
        'saldo',
        'importe',
    ];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'id_item', 'id_item');
    }
}
