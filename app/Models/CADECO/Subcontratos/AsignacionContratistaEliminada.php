<?php


namespace App\Models\CADECO\Subcontratos;


use Illuminate\Database\Eloquent\Model;

class AsignacionContratistaEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.asignaciones_eliminadas';
    protected $primaryKey = 'id_asignacion';

    public $timestamps = false;

    protected $fillable = [
        'id_asignacion',
        'id_transaccion',
        'registro',
        'fecha_hora_registro',
        'autorizo',
        'fecha_hora_autorizacion',
        'estado',
        'usuario_elimina',
        'motivo_eliminacion',
        'fecha_eliminacion'
    ];
}
