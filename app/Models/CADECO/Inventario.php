<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 05:51 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'inventarios';
    protected $primaryKey = 'id_lote';

    public $timestamps = false;

    protected $fillable = [
        'id_item',
        'id_almacen',
        'id_material',
        'cantidad',
        'saldo',
        'monto_total',
        'monto_pagado',
    ];

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen', 'id_almacen');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }

    public function item()
    {
        return $this->hasMany(Item::class, 'id_item', 'id_item');
    }

    public function getCantidadFormatAttribute()
    {
        return number_format($this->cantidad,3,'.', '');
    }

    public function getSaldoFormatAttribute()
    {
        return number_format($this->saldo,3,'.', '');
    }
}
