<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 12/07/2019
 * Time: 01:25 PM
 */

namespace App\Models\CADECO;


class FacturaPartida extends Item
{
    public function factura()
    {
        return $this->belongsTo(Factura::class, 'id_transaccion', 'id_transaccion');
    }
}