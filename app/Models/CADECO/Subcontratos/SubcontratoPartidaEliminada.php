<?php


namespace App\Models\CADECO\Subcontratos;


use Illuminate\Database\Eloquent\Model;

class SubcontratoPartidaEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.subcontratos_partidas_eliminadas';
    protected $primaryKey = 'id_item';
    public $timestamps = false;

    protected $fillable = [
        'id_item',
        'id_transaccion',
        'id_antecedente',
        'item_antecedente',
        'id_concepto',
        'id_material',
        'unidad',
        'cantidad',
        'cantidad_material',
        'cantidad_mano_obra',
        'importe',
        'saldo',
        'anticipo',
        'descuento',
        'precio_unitario',
        'precio_material',
        'precio_mano_obra',
    ];
}
