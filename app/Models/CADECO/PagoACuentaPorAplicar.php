<?php


namespace App\Models\CADECO;


class PagoACuentaPorAplicar extends Pago
{
    public const TIPO_ANTECEDENTE = null;
    public const OPCION_ANTECEDENTE = null;

    protected $fillable = [
        'tipo_transaccion',
        'fecha',
        'estado',
        'id_obra',
        'id_costo',
        "id_cuenta",
        "id_empresa",
        "id_moneda",
        'cumplimiento',
        'vencimiento',
        'monto',
        "saldo",
        "tipo_cambio",
        'referencia',
        "destino",
        'observaciones',
    ];
    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 82)
                ->whereNull('id_antecedente')
                ->whereNull('id_referente')
                ->where('opciones', '=', 327681)
                ->where('estado', '!=', -2);
        });
    }
}
