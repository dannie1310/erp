<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:12 AM
 */

namespace App\Models\CADECO;


class PagoACuenta extends Pago
{
    public const TIPO_ANTECEDENTE = 72;
    public const OPCION_ANTECEDENTE = 327681;
    public const OPCION = 327681;

    protected $fillable = [
        'id_antecedente',
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
                ->where('opciones', '=', 327681)
                ->where('estado', '!=', -2);
        });
    }

    public function solicitud(){
        return $this->belongsTo(SolicitudPagoAnticipado::class, 'id_antecedente', 'id_transaccion');
    }
}
