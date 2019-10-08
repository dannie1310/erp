<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:13 AM
 */

namespace App\Models\CADECO;


class ReposicionFondoFijo extends Solicitud
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('opciones', '=', 1)
                ->where('estado', '!=', -2);
        });
    }
}