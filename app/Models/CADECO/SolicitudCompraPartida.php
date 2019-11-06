<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 05/11/2019
 * Time: 04:56 p. m.
 */


namespace App\Models\CADECO;


class SolicitudCompraPartida extends Item
{
    protected $fillable = [
        'id_item',
        'id_transaccion',
        'id_material',
        'unidad',
        'cantidad',
        'id_concepto',
        'id_almacen'
    ];
}
