<?php


namespace App\Models\CADECO;


class ItemComprobanteFondo extends Item
{
    protected $fillable = [
        'id_item',
        'id_transaccion',
        'id_concepto',
        'cantidad',
        'importe',
        'referencia',
        'estado'
    ];


    /**
     * Relaciones Eloquent
     */


    /**
     * Scopes
     */


    /**
     * Attributes
     */


    /**
     * Métodos
     */
}
