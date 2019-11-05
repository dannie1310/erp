<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 12/09/2019
 * Time: 12:48 PM
 */

namespace App\Models\CADECO;


class AjustePositivoPartida extends Item
{
    protected $fillable = [
        'id_transaccion',
        'item_antecedente',
        'cantidad',
        'importe',
        'id_almacen',
        'id_material',
        'referencia'
    ];

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'item_antecedente', 'id_lote');
    }
}