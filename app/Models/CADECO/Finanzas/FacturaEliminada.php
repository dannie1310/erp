<?php


namespace App\Models\CADECO\Finanzas;

use Illuminate\Database\Eloquent\Model;

class FacturaEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.facturas_eliminadas';
    protected $primaryKey = 'id_transaccion';

    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'vencimiento',
        'estado',
        'id_obra',
        'id_empresa',
        'monto',
        'saldo',
        'observaciones',
        'FechaHoraRegistro',
        'id_usuario_registro',
        'motivo_elimino',
    ];
}