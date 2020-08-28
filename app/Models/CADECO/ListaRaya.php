<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/01/2020
 * Time: 05:08 PM
 */

namespace App\Models\CADECO;


class ListaRaya extends Transaccion
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 99)
                ->where('opciones', '=', 0);
        });
    }

    public function items()
    {
        return $this->hasMany(ItemListaRaya::class, 'id_transaccion', 'id_transaccion');
    }

    public function movimientos()
    {
        return $this->hasManyThrough(Movimiento::class, ItemListaRaya::class, "id_transaccion", "id_item", "id_transaccion", "id_item");
    }

    /**
     * Este método implementa la lógica actualización de control de obra del procedimiento almacenado sp_aplicar_pagos
     * y se detona al registrar una orden de pago relacionada a una factura que ampara una lista de raya
     */
    public function actualizaControlObra(ItemFactura $item_factura, OrdenPago $orden_pago)
    {
        if($this->items)
        {
            foreach($this->items as $item)
            {
                $item->inventario->monto_pagado = $item->importe;
                $item->inventario->save();
                $item->inventario->distribuirPagoInventarios();
            }
        }
    }

    /**
     * Implementa lógica de SP: sp_desaplicar_pago
     */
    public function desaplicaPago()
    {
        if($this->items)
        {
            foreach ($this->items as $item)
            {
                $item->inventario->update([
                    'monto_pagado' => 0
                ]);
                $item->inventario->distribuirPagoInventarios();
            }
        }
    }
}
