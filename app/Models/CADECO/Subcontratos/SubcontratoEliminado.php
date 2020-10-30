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
        'fecha',
        'id_obra',
        'id_empresa',
        'id_moneda',
        'anticipo',
        'anticipo_monto',
        'anticipo_saldo',
        'monto',
        'PorcentajeDescuento',
        'impuesto',
        'impuesto_retenido',
        'id_costo',
        'retencion',
        'referencia',
        'observaciones',
        'id_usuario'
    ];
}
