<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/12/2019
 * Time: 01:03 PM
 */

namespace App\Models\CADECO;


class Deposito extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const NOMBRE = "Deposito";

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 81);
        });
    }
}
