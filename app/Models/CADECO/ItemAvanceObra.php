<?php


namespace App\Models\CADECO;


class ItemAvanceObra extends Item
{
    protected $fillable = [
        'id_transaccion',
        'id_concepto',
        'cantidad',
        'precio_unitario',
        'importe',
        'numero'
    ];

}
