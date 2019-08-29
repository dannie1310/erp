<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 05:59 PM
 */

namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class ItemEntradaEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.items_entradas_eliminadas';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'id_item',
        'id_transaccion',
        'id_antecedente',
        'item_antecedente',
        'id_almacen',
        'id_concepto',
        'id_material',
        'unidad',
        'numero',
        'cantidad',
        'cantidad_material',
        'importe',
        'saldo',
        'precio_unitario',
        'anticipo',
        'precio_material',
        'referencia',
        'estado',
        'cantidad_original1',
        'precio_original1',
        'id_asignacion'
    ];

    public $timestamps = false;
}