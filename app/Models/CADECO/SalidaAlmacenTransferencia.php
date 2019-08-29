<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 06:12 PM
 */

namespace App\Models\CADECO;


class SalidaAlmacenTransferencia extends Transaccion
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 34)
                ->where('opciones', '=', 65537);
        });
    }
}