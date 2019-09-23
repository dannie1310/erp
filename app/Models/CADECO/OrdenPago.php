<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:10 AM
 */

namespace App\Models\CADECO;


class OrdenPago extends Transaccion
{
    public const TIPO_ANTECEDENTE = 67;

    protected $fillable = [
        'id_antecedente',
        'id_referente',
        'fecha',
        'id_obra',
        'monto',
        'referencia',
        'tipo_transaccion',
        "id_empresa",
        "id_moneda",
        "id_usuario"
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 68)
                ->where('opciones', '=', 0)
                ->where('estado', '!=', -2);
        });
    }
}
