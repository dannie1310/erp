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
    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'id_item', 'id_item');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_transaccion', 'id_transaccion');
    }

    public function getImporteFormatAttribute(){
        return '$ ' . number_format($this->importe,2, '.', ',');
    }

    public function getPrecioUnitarioFormatAttribute(){
        return '$ ' . number_format($this->precio_unitario, 2, '.', ',');
    }

}