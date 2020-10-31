<?php


namespace App\Models\CADECO\Subcontratos;


use Illuminate\Database\Eloquent\Model;

class AsignacionContratistaPartidaEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.partidas_asignacion_eliminadas';
    protected $primaryKey = 'id_partida_asignacion';

    public $timestamps = false;

    protected $fillable = [
        'id_partida_asignacion',
        'id_transaccion',
        'id_asignacion',
        'id_concepto',
        'cantidad_asignada',
        'cantidad_autorizada',
    ];
}
