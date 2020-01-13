<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 08/01/2020
 * Time: 07:04 PM
 */

namespace App\Models\CADECO;


class ItemOrdenRenta extends Item
{
    public function ordenCompra()
    {
        return $this->belongsTo(OrdenRenta::class, 'id_transaccion', 'id_transaccion');
    }

    public function entrega()
    {
        return $this->hasOne(Entrega::class, 'id_item');
    }

    public function entradas()
    {
        return $this->hasMany(EntradaMaquinaria::class, 'item_antecedente', 'id_item');
    }

    public function items_entrada()
    {
        return $this->hasMany(ItemEntradaMaquinaria::class, "item_antecedente", "id_item");
    }

    public function itemExtensionRenta()
    {
        return $this->belongsTo(ItemExtensionRenta::class, 'item_antecedente', 'id_item');
    }

    public function facturasPartida()
    {
        return $this->hasMany(FacturaPartida::class, 'item_antecedente', 'id_item');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material');
    }

    public function getPagadoAttribute()
    {
        $pagado = 0;
        if ($this->facturasPartida) {
            $pagado = round($this->facturasPartida->sum("importe") - $this->facturasPartida->sum("saldo"), 2);
        }
        return $pagado;
    }

    public function getAplicadoAttribute()
    {
        $aplicado = round(($this->importe * $this->anticipo / 100) - $this->saldo, 2);
        return $aplicado;
    }

    public function getPorAplicarAttribute()
    {
        return round($this->pagado - $this->aplicado, 2);
    }

    public function disminuyeSaldo($monto)
    {
        $saldo = $this->saldo - $monto;
        /*Se realiza esta validación por el error existente en el registro de partida de ordenes de compra con anticipo
        cuyo saldo queda en 0*/
        if ($saldo < -0.01) {
            $saldo = 0;
        }
        $this->saldo = $saldo;
        $this->save();
    }

    public function aumentaSaldo($monto)
    {
        $saldo = $this->saldo + $monto;
        /*Se realiza esta validación por el error existente en el registro de partida de ordenes de compra con anticipo
        cuyo saldo queda en 0*/
        if ($saldo > $this->importe) {
            $saldo = $this->importe * $this->anticipo / 100;
        }
        $this->saldo = $saldo;
        $this->save();
    }

    /**
     * Este método implementa la lógica actualización de control de obra del procedimiento almacenado sp_aplicar_pagos
     * y se detona al registrar una orden de pago
     */
    public function actualizaControlObra(ItemFactura $item_factura, OrdenPago $orden_pago)
    {
        $importe = round($orden_pago->monto * -1 * $item_factura->proporcion_item, 2);
        $this->ordenCompra->anticipo_saldo = round($this->ordenCompra->anticipo_saldo - round($importe * $item_factura->factura->factor_iva, 2), 2);
        $this->ordenCompra->save();
        $this->amortizaAnticipo($importe);
    }

    /**
     * Este método implementa la lógica del procedimiento almacenado sp_amortizar_anticipo
     * y se detona al registrar una orden de pago para el anticipo de una orden de renta
     */
    public function amortizaAnticipo($importe_pagado)
    {
        $monto_amortizado = 0;
        if ($this->numero > 0) {
            $items_entrada = $this->items_entrada;
            if ($items_entrada) {
                foreach ($items_entrada as $item_entrada) {
                    if ($monto_amortizado < $importe_pagado) {
                        if ($item_entrada->inventario->numero < $this->numero) {
                            $monto_xamortizar = round($importe_pagado * $item_entrada->inventario->numero / $this->numero, 2);
                        } else {
                            $monto_xamortizar = $importe_pagado;
                        }

                        if ($monto_xamortizar > ($importe_pagado - $monto_amortizado)) {
                            $monto_xamortizar = $importe_pagado - $monto_amortizado;
                        }
                        $monto_anticipo = $item_entrada->inventario->monto_anticipo + $monto_xamortizar;
                        $monto_amortizado = $monto_amortizado + $monto_xamortizar;
                        $monto_pagado = $item_entrada->inventario->monto_pagado;
                        $monto_total = $item_entrada->inventario->monto_total;
                        if ($monto_total > $monto_pagado) {
                            if ($monto_anticipo > ($monto_total - $monto_pagado)) {
                                $monto_anticipo = $monto_anticipo - ($monto_total - $monto_pagado);
                                $monto_pagado = $monto_total;
                            } else {
                                $monto_pagado = $monto_pagado + $monto_anticipo;
                                $monto_anticipo = 0;

                            }
                            $item_entrada->inventario->monto_pagado = $monto_pagado;
                            $item_entrada->inventario->monto_anticipo = $monto_anticipo;
                            $item_entrada->inventario->save();
                            $item_entrada->inventario->distribuirPagoInventarios();
                        } else {
                            $item_entrada->inventario->monto_anticipo = $monto_anticipo;
                            $item_entrada->inventario->save();
                        }

                    } else {
                        break;
                    }
                }
            }
            $this->saldo = $this->saldo - $monto_amortizado;
            $this->save();

        } else {
            if ($this->itemExtensionRenta->inventario) {
                $monto_total = $this->itemExtensionRenta->inventario->monto_total;
                $monto_pagado = $this->itemExtensionRenta->inventario->monto_pagado;
                if ($monto_total >= $monto_pagado) {
                    if (($monto_total - $monto_pagado) > $importe_pagado) {
                        $this->itemExtensionRenta->inventario->monto_pagado += $importe_pagado;
                        $this->itemExtensionRenta->inventario->save();
                    } else {
                        $this->itemExtensionRenta->inventario->monto_pagado += ($monto_total - $monto_pagado);
                        $this->itemExtensionRenta->inventario->monto_anticipo += ($importe_pagado - ($monto_total - $monto_pagado));
                        $this->itemExtensionRenta->inventario->save();
                    }
                } else {
                    $this->itemExtensionRenta->inventario->monto_anticipo += $importe_pagado;
                    $this->itemExtensionRenta->inventario->save();
                }
                $this->itemExtensionRenta->inventario->distribuirPagoInventarios();
                $this->saldo = $this->saldo - $monto_amortizado;
                $this->save();
            }
        }
    }
}