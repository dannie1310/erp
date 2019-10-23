<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 22/10/2019
 * Time: 09:45 PM
 */

namespace App\Models\CADECO;

class Anticipo extends Transaccion
{
    public const TIPO_ANTECEDENTE = 82;
    public const OPCION_ANTECEDENTE = 131073;

    protected $fillable = [
        'id_antecedente',
        'id_referente',
        'fecha',
        "id_empresa",
        "id_moneda",
        'monto',
        "saldo",
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 66)
                ->where('opciones', '=', 0)
                ->where('estado', '!=', -2);
        });
    }

    public function pago(){
        return $this->belongsTo(PagoAnticipoDestajo::class, 'id_antecedente', 'id_transaccion');
    }

    public function transaccion(){
        return $this->belongsTo(Transaccion::class, 'id_referente', 'id_transaccion');
    }
}
