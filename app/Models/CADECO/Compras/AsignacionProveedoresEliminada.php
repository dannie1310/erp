<?php


namespace App\Models\CADECO\Compras;

use Illuminate\Database\Eloquent\Model;

class AsignacionProveedoresEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.asignacion_proveedores_eliminadas';
    protected $primaryKey = 'id_asignacion';

    public $timestamps = false;

    protected $fillable = [
        'id_asignacion',
        'id_solicitud',
        'id_empresa',
        'registro',
        'fecha_registro',
        'id_usuario_elimino',
        'motivo_elimino',
        'partidas'
    ];
}