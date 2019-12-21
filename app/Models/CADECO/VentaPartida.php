<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/12/2019
 * Time: 07:36 PM
 */

namespace App\Models\CADECO;

use App\Models\CADECO\Inventario;


class VentaPartida extends Item
{
    protected $fillable = [
        'id_transaccion',
        'item_antecedente',
        'cantidad',
        'saldo',
        'cantidad_material',
        'importe',
        'precio_unitario',
        'precio_material',
        'id_material',
        'unidad',
        'cantidad_original1'
    ];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'item_antecedente', 'id_lote');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_transaccion', 'id_transaccion');
    }

    public function getImporteFormatAttribute()
    {
        return '$ ' . number_format($this->importe,2, '.', ',');
    }

    public function getPrecioUnitarioFormatAttribute()
    {
        return '$ ' . number_format($this->precio_unitario, 2, '.', ',');
    }

    public function aumentarSaldoInventario()
    {
        $this->inventario->saldo = $this->inventario->saldo + $this->cantidad;
        $this->inventario->save();
    }
}