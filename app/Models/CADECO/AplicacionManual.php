<?php


namespace App\Models\CADECO;


class AplicacionManual extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 70);
        });
    }
}
