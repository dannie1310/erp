<?php

namespace App\Models\CONTROL_RECURSOS;


class CuentaPagadoraSantanderEmpresa extends CuentaPagadoraEmpresa
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where("IdBanco","=","3");
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
