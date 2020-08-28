<?php


namespace App\Models\CADECO\Contratos;


use Illuminate\Database\Eloquent\Model;

class ContratoEliminado extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Contratos.contratos_eliminados';
    protected $primaryKey = 'id_concepto';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_concepto',
        'nivel',
        'descripcion',
        'id_destino',
        'unidad',
        'cantidad_original',
        'cantidad_presupuestada',
        'cantidad_modificada',
        'estado',
        'clave',
        'id_marca',
        'id_modelo',
        'usuario_elimina',
        'fecha_eliminacion'
    ];
}
