<?php


namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class CotizacionEliminada extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.cotizaciones_eliminadas';
    protected $primaryKey = 'id_transaccion';

    protected $fillable = [
        'id_transaccion',
        'id_antecedente',
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'estado',
        'id_obra',
        'id_empresa',
        'id_sucursal',
        'id_moneda',
        'cumplimiento',
        'vencimiento',
        'opciones',
        'monto',
        'saldo',
        'autorizado',
        'impuesto',
        'referencia',
        'comentario',
        'observaciones',
        'FechaHoraRegistro',
        'porcentaje_anticipo_pactado',
        'id_usuario',
        'parcialidades',
        'dias_credito',
        'vigencia',
        'plazo_entrega',
        'descuento',
        'anticipo',
        'importe',
        'tc_usd',
        'tc_eur',
        'registro',
        'timestamp_registro',
        'motivo',
        'usuario_elimina',
        'fecha_eliminacion'
    ];

    public $timestamps = false;
}
