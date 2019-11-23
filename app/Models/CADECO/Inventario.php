<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 05:51 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\CADECO\Compras\InventarioEliminado;

class Inventario extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'inventarios';
    protected $primaryKey = 'id_lote';

    public $timestamps = false;

    public $searchable = [
        'id_material'
    ];

    protected $fillable = [
        'id_item',
        'id_almacen',
        'id_material',
        'cantidad',
        'lote_antecedente',
        'saldo',
        'monto_total',
        'monto_original',
        'monto_pagado',
        'monto_anticipo',
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

    public function respaldar()
    {
        InventarioEliminado::create(
            [
                'id_lote' => $this->id_lote,
                'lote_antecedente' => $this->lote_antecedente,
                'id_almacen' => $this->id_almacen,
                'id_material' => $this->id_material,
                'id_item' => $this->id_item,
                'saldo' => $this->saldo,
                'monto_total' => $this->monto_total,
                'monto_pagado' => $this->monto_pagado,
                'monto_aplicado' => $this->monto_aplicado,
                'fecha_desde' => $this->fecha_desde,
                'referencia' => $this->referencia,
                'monto_original' => $this->monto_original
            ]
        );
    }

}