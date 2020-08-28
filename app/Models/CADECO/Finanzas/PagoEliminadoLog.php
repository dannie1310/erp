<?php


namespace App\Models\CADECO\Finanzas;


use Illuminate\Database\Eloquent\Model;

class PagoEliminadoLog extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.pagos_eliminados_log';
    protected $primaryKey = 'id_transaccion';

    protected $fillable = [
        'id_transaccion',
        'consulta',
        'usuario_elimina',
        'fecha_eliminacion'
    ];

    public $timestamps = false;
}

