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
}