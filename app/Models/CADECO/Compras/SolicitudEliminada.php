<?php


namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class SolicitudEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.solicitudes_eliminadas';
    protected $primaryKey = 'id_transaccion';

    protected $fillable = [
        'id_transaccion',
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'id_obra',
        'opciones',
        'comentario',
        'observaciones',
        'id_usuario',
        'FechaHoraRegistro',
        'id_area_compradora',
        'id_tipo',
        'id_area_solicitante',
        'folio_compuesto',
        'concepto',
        'fecha_requisicion_origen',
        'requisicion_origen',
        'usuario_elimina',
        'motivo',
        'fecha_eliminacion'
    ];

    public $timestamps = false;
}
