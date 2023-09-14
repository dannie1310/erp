<?php

namespace App\Models\CONTROL_RECURSOS;


class CuentaPagadoraEmpresa extends CuentaEmpresa
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('IdTipoCuenta', '=', 3);
        });
    }


    /**
     * Relaciones
     */


    /**
     * Scopes
     */

    /**
     * Atributos
     */

    /**
     * Métodos
     */
}
