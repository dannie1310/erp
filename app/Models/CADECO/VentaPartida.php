<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/12/2019
 * Time: 07:36 PM
 */

namespace App\Models\CADECO;


class VentaPartida extends Item
{

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_transaccion', 'id_transaccion');
    }

}