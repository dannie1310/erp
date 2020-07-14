<?php


namespace App\Models\CADECO\Contratos;

use Illuminate\Database\Eloquent\Model;

class PresupuestoContratistaEliminado extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Contratos.presupuesto_contratista_eliminados';
    protected $primaryKey = 'id_transaccion';

    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_contrato_proyectado',
        'id_sucursal',
        'id_empresa',
        'monto',
        'impuesto',
        'TcUSD',
        'TcEuro',
        'registro',
        'partidas',
        'fecha_registro',
        'motivo_elimino',
        'id_usuario_elimino',
        'fecha_hora_elimino'
    ];
}