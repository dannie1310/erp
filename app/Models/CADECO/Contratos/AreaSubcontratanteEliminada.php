<?php


namespace App\Models\CADECO\Contratos;


use Illuminate\Database\Eloquent\Model;

class AreaSubcontratanteEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Contratos.areas_subcontratantes_eliminadas';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_area_subcontratante',
        'id_usuario',
        'timestamp_registro',
        'usuario_elimina',
        'fecha_eliminacion'
    ];
}
