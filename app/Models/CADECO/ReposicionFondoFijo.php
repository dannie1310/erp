<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 10:13 AM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class ReposicionFondoFijo extends Model
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 72)
                ->where('opciones', '=', 1)
                ->where('estado', '!=', -2);
        });
    }
}