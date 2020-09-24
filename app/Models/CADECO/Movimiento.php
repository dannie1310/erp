<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 05:53 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'movimientos';
    protected $primaryKey = 'id_movimiento';

    protected $fillable = [
        'id_item',
        'id_concepto',
        'id_material',
        'cantidad',
        'lote_antecedente',
        'creado',
        'monto_total',
        'monto_pagado',
        'monto_original',
    ];

    public $timestamps = false;


    public function items()
    {
        return $this->belongsTo(Item::class, 'id_item', 'id_item');
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'lote_antecedente', 'id_lote');
    }

    public function getCantidadFormatAttribute()
    {
        return number_format($this->cantidad,3,'.', '');

    }
}
