<?php


namespace App\Models\CADECO\Subcontratos;


use Illuminate\Database\Eloquent\Model;

class AvanceSubcontratoEliminado extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.avance_subcontratos_eliminados';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_antecedente',
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'estado',
        'id_obra',
        'id_empresa',
        'id_moneda',
        'cumplimiento',
        'vencimiento',
        'opciones',
        'monto',
        'saldo',
        'impuesto',
        'referencia',
        'comentario',
        'observaciones',
        'FechaHoraRegistro',
        'fecha_ejecucion',
        'fecha_contable',
        'id_usuario',
        'fecha_eliminacion',
        'id_usuario_elimino',
        'motivo',
    ];
}
