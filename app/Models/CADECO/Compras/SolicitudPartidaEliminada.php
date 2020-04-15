<?php


namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class SolicitudPartidaEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.solicitudes_partidas_eliminadas';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'id_item',
        'id_transaccion',
        'id_material',
        'unidad',
        'cantidad',
        'estado',
        'cantidad_original1',
        'fecha_entrega',
        'observaciones'
    ];

    public $timestamps = false;
}
