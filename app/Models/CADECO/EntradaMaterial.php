<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 15/05/2019
 * Time: 07:04 PM
 */

namespace App\Models\CADECO;


class EntradaMaterial extends Transaccion
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 33)
                ->where('opciones', '=', 1)
                ->where('estado', '!=', -2);
        });
    }
}