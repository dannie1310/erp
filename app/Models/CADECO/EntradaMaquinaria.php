<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 07/01/2020
 * Time: 08:47 PM
 */

namespace App\Models\CADECO;


class EntradaMaquinaria extends Transaccion
{
    public const TIPO_ANTECEDENTE = 19;
    public const OPCION_ANTECEDENTE = 8;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 33)
                ->where('opciones', '=', 8);
        });
    }

    public function items()
    {
        return $this->hasMany(ItemEntradaMaquinaria::class, 'id_transaccion', 'id_transaccion');
    }
}