<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:10 AM
 */

namespace App\Models\CADECO;

class PagoFactura extends Pago
{
    public const TIPO_ANTECEDENTE = null;

    protected $fillable = [
        'id_antecedente',
        'numero_folio',
        'fecha',
        'id_obra',
        'cumplimiento',
        'vencimiento',
        'monto',
        'referencia',
        'observaciones',
        'tipo_transaccion',
        "id_cuenta",
        "id_empresa",
        "id_moneda",
        "saldo",
        "destino",
        "id_usuario"
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('opciones', '=', 0);
        });
    }

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function orden_pago(){
        return $this->belongsTo(OrdenPago::class, 'numero_folio', 'numero_folio');
    }
    //TODO: Generar relación con factura, manythroug causa problemas por que los modelos tienen el mismo nombre de tabla
}
