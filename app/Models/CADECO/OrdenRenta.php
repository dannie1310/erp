<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/01/2020
 * Time: 07:14 PM
 */

namespace App\Models\CADECO;


class OrdenRenta extends Transaccion
{
    public const TIPO_ANTECEDENTE = 17;
    public const OPCION_ANTECEDENTE = 8;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope('tipo',function ($query) {
            return $query->where('tipo_transaccion', '=', 19)
                ->where('opciones', '=', 8)
                ->where('estado', '!=', -2);
        });
    }
}