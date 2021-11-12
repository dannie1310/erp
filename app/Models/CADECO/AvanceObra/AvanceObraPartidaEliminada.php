<?php


namespace App\Models\CADECO\AvanceObra;


use Illuminate\Database\Eloquent\Model;

class AvanceObraPartidaEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'AvanceObra.avances_obra_partidas_eliminadas';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public $fillable = [
        'id_item',
        'id_transaccion',
        'id_concepto',
        'numero',
        'cantidad',
        'importe',
        'precio_unitario',
        'estado'
    ];
}
