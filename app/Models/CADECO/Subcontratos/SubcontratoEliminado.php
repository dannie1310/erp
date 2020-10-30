<?php


namespace App\Models\CADECO\Subcontratos;


use Illuminate\Database\Eloquent\Model;

class SubcontratoEliminado extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.subcontratos_eliminados';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_antecedente',
        'id_referente',
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'estado',
        'id_obra',
        'id_empresa',
        'id_moneda',
        'opciones',
        'monto',
        'saldo',
        'autorizado',
        'impuesto',
        'impuesto_retenido',
        'diferencia',
        'anticipo',
        'anticipo_monto',
        'anticipo_saldo',
        'PorcentajeDescuento',
        'impuesto',
        'impuesto_retenido',
        'retencion',
        'referencia',
        'observaciones',
        'tipo_cambio',
        'comentario',
        'TipoLiberacion',
        'FechaHoraRegistro',
        'TcUSD',
        'TcEuro',
        'DiasCredito',
        'DiasVigencia',
        'descuento',
        'porcentaje_anticipo_pactado',
        'fecha_ejecucion',
        'fecha_contable',
        'anticipo_pactado_monto',
        'id_usuario',
        'retencionIVA_2_3'
    ];
}
