<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/12/2019
 * Time: 01:04 PM
 */

namespace App\Models\CADECO;


class DepositoCliente extends Deposito
{
    protected $fillable = [
        'id_antecedente',
        'id_cuenta',
        'id_empresa',
        'id_moneda',
        'cumplimiento',
        'vencimiento',
        'monto',
        'referencia',
        'observaciones',
        'tipo_transaccion',
        'opciones',
        'estado',
        'numero_folio',
        'fecha',
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('opciones', '=', 4);
        });
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_antecedente', 'id_transaccion');
    }

    public function cuenta(){
        return $this->belongsTo(cuenta::class, 'id_cuenta', 'id_cuenta');
    }
}