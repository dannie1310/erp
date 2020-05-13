<?php


namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class EntregaEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.entregas_eliminadas';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'id_item',
        'numero_entrega',
        'fecha',
        'cantidad',
        'surtida',
        'pedidas',
        'asignadas',
        'id_concepto',
        'id_almacen'
    ];

    public $timestamps = false;

}
