<?php


namespace App\Models\CADECO;


class Familia extends Material
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereRaw('LEN(nivel) = 4');
        });
    }
}
