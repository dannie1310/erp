<?php


namespace App\Models\CADECO\Contratos;


use Illuminate\Database\Eloquent\Model;

class ContratoProyectadoEliminado extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Contratos.contratos_proyectados_eliminados';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'estado',
        'impreso',
        'id_obra',
        'cumplimiento',
        'vencimiento',
        'opciones',
        'referencia',
        'comentario',
        'observaciones',
        'FechaHoraRegistro',
        'id_usuario',
        'motivo',
        'usuario_elimina',
        'fecha_eliminacion'
    ];
}
