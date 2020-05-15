<?php

namespace App\Models\CADECO;



class PresupuestoContratista extends Transaccion
{
    public const TIPO_ANTECEDENTE = 49;


    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            return $query->where('tipo_transaccion', '=', 50);
        });
    }
}
