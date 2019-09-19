<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/09/2019
 * Time: 01:20 PM
 */

namespace App\Models\CADECO;


class NuevoLote extends Ajuste
{

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('opciones', '=', 2);
        });
    }

    public function partidas()
    {
        return $this->hasMany(NuevoLotePartida::class, 'id_transaccion', 'id_transaccion');
    }
}