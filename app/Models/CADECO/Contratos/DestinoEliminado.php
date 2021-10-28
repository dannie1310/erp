<?php


namespace App\Models\CADECO\Contratos;


use Illuminate\Database\Eloquent\Model;

class DestinoEliminado extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Contratos.destinos_eliminados';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_concepto_contrato',
        'id_concepto',
        'id_concepto_original',
        'usuario_elimina',
        'fecha_eliminacion'
    ];
}
