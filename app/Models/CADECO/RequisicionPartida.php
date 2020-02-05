<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 14/11/2019
 * Time: 01:54 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Compras\RequisicionPartidaComplemento;

class RequisicionPartida extends Item
{
    protected $fillable = [
        'id_item',
        'id_transaccion',
        'id_material',
        'id_concepto',
        'unidad',
        'cantidad'
    ];

    public function complemento()
    {
        return $this->belongsTo(RequisicionPartidaComplemento::class, 'id_item', 'id_item');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }

    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'id_concepto', 'id_concepto');
    }
}