<?php

namespace App\Models\CONTROL_RECURSOS;

class Factura extends Documento
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereIn('IdTipoDocto', [1,6])->where('Estatus', '=', 1);
        });
    }
}
