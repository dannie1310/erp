<?php


namespace App\Models\CADECO\Finanzas;


use Illuminate\Database\Eloquent\Model;

class PagoEliminado extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.pagos_eliminados';
    protected $primaryKey = 'id_transaccion';

    protected $fillable = [
        'id_transaccion',
        'id_antecedente',
        'id_referente',
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'estado',
        'impreso',
        'id_obra',
        'id_cuenta',
        'id_empresa',
        'id_moneda',
        'cumplimiento',
        'vencimiento',
        'opciones',
        'monto',
        'saldo',
        'autorizado',
        'impuesto',
        'impuesto_retenido',
        'diferencia',
        'anticipo_monto',
        'anticipo_saldo',
        'anticipo',
        'retencion',
        'tipo_cambio',
        'referencia',
        'destino',
        'comentario',
        'observaciones',
        'FechaHoraRegistro',
        'id_usuario',
        'retencionIVA_2_3',
        'motivo',
        'usuario_elimina',
        'fecha_eliminacion'
    ];

    public $timestamps = false;
}
