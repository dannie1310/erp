<?php


namespace App\Models\CADECO\Estimaciones;


use Illuminate\Database\Eloquent\Model;

class SolicitudAutorizacionAvanceEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Estimaciones.solicitudes_autorizacion_avance_eliminadas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_transaccion',
        'id_antecedente',
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'estado',
        'impreso',
        'id_obra',
        'id_empresa',
        'id_moneda',
        'cumplimiento',
        'vencimiento',
        'opciones',
        'monto',
        'saldo',
        'autorizado',
        'impuesto',
        'anticipo',
        'tipo_cambio',
        'comentario',
        'observaciones',
        'FechaHoraRegistro',
        'IVARetenido',
        'id_usuario',
        'usuario_elimina',
        'motivo_eliminacion',
        'fecha_eliminacion'
    ];

    public $timestamps = false;
}
