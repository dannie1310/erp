<?php


namespace App\Models\CADECO\Subcontratos;


use Illuminate\Database\Eloquent\Model;

class AvanceSubcontratoPartidaEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.avance_subcontratos_partidas_eliminadas';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_item',
        'id_transaccion',
        'id_antecedente',
        'id_concepto',
        'cantidad',
        'importe',
        'precio_unitario'
    ];
}
