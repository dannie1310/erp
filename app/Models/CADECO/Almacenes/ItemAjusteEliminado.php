<?php


namespace App\Models\CADECO\Almacenes;


use Illuminate\Database\Eloquent\Model;

class ItemAjusteEliminado extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Almacenes.items_ajustes_eliminados';
    protected $primaryKey = 'id_item';

    public $timestamps = false;

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
}
