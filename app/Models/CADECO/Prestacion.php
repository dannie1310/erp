<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/01/2020
 * Time: 05:09 PM
 */

namespace App\Models\CADECO;


class Prestacion extends Transaccion
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 102)
                ->where('opciones', '=', 0);
        });
    }

    public function items()
    {
        return $this->hasMany(ItemPrestacion::class, 'id_transaccion', 'id_transaccion');
    }

    /**
     * Este método implementa la lógica actualización de control de obra del procedimiento almacenado sp_aplicar_pagos
     * y se detona al registrar una orden de pago relacionada a una factura que ampara una prestación
     */
    public function actualizaControlObra(ItemFactura $item_factura, OrdenPago $orden_pago)
    {
        $importe = round($orden_pago->monto * -1 * $item_factura->proporcion_item, 2);
        $factor = $importe / $this->monto;
        if ($this->items) {
            foreach ($this->items as $item) {
                $pago = round(($item->inventario->monto_pagado + $item->importe * $factor), 2);
                $item->inventario->monto_pagado = $pago;
                $item->inventario->save();
                $item->inventario->distribuirPagoInventarios();
            }
        }
    }

    /**
     * Implementa lógica de SP: sp_desaplicar_pago
     */
    public function desaplicaPago($factor)
    {
        if($this->items)
        {
            foreach ($this->items as $item)
            {
                $monto_pagado = ROUND(($item->inventario->monto_pagado - $item->importe * $factor), 2);
                $item->inventario->update([
                    'monto_pagado' => $monto_pagado
                ]);
                $item->inventario->distribuirPagoInventarios();
            }
        }
    }
}
