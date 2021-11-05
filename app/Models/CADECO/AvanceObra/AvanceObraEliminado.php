<?php


namespace App\Models\CADECO\AvanceObra;


use Illuminate\Database\Eloquent\Model;

class AvanceObraEliminado extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'AvanceObra.avances_obra_eliminados';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public $fillable = [
        'id_transaccion',
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'estado',
        'id_obra',
        'id_concepto',
        'cumplimiento',
        'vencimiento',
        'opciones',
        'monto',
        'impuesto',
        'comentario',
        'observaciones',
        'FechaHoraRegistro',
        'id_usuario',
        'motivo',
        'usuario_elimina',
        'fecha_eliminacion'
    ];
}
