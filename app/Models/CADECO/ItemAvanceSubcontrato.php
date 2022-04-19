<?php


namespace App\Models\CADECO;


class ItemAvanceSubcontrato extends Item
{

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->whereHas('avance');
        });
    }

    /**
     * Relaciones
     */
    public function avance()
    {
        return $this->belongsTo(AvanceSubcontrato::class, 'id_transaccion', 'id_transaccion');
    }

    /**
     * Atributos
     */


    /**
     * Scope
     */

    /**
     * Acciones
     */
}
