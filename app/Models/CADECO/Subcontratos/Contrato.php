<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 01:48 PM
 */

namespace App\Models\CADECO\Subcontratos;


use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 49)
                ->where('estado', '!=', -2);
        });
    }
}