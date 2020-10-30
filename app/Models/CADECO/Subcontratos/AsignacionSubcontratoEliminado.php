<?php


namespace App\Models\CADECO\Subcontratos;


use Illuminate\Database\Eloquent\Model;

class AsignacionSubcontratoEliminado extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.asignaciones_subcontratos_eliminados';
    protected $primaryKey = 'id_asignacion';

    public $timestamps = false;

    protected $fillable = [
        'id_asignacion',
        'id_transaccion',
    ];
}
