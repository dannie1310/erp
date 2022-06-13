<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 03/01/2020
 * Time: 05:35 PM
 */

namespace App\Models\CADECO;
use App\Models\CADECO\Compras\AsignacionProveedorPartida;
use App\Models\CADECO\Compras\OrdenCompraPartidaComplemento;

class ItemOrdenCompra extends Item
{
    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'id_transaccion', 'id_transaccion');
    }

    public function orden_partida_complemento()
    {
        return $this->belongsTo(OrdenCompraPartidaComplemento::class, 'id_item');
    }

    public function entrega()
    {
        return $this->hasOne(Entrega::class, 'id_item');
    }

    public function entradas()
    {
        return $this->hasMany(EntradaMaterialPartida::class, 'item_antecedente', 'id_item');
    }

    public function items_entrada()
    {
        return $this->hasMany(ItemEntradaAlmacen::class, "item_antecedente", "id_item");
    }

    public function facturasPartida()
    {
        return $this->hasMany(FacturaPartida::class, 'item_antecedente', 'id_item');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material');
    }

    public function itemSolicitudCompra()
    {
        return $this->belongsTo(ItemSolicitudCompra::class,"item_antecedente", "id_item");
    }

    public function partidaAsignacion()
    {
        return $this->belongsTo(AsignacionProveedorPartida::class,"item_antecedente", "id_item_solicitud")
            ->where("id_asignacion_proveedores","=",$this->ordenCompra->complemento->asignacion->id)
            ->where("id_transaccion_cotizacion","=",$this->ordenCompra->cotizacion->id_transaccion)
            ->where("id_transaccion_cotizacion","=",$this->ordenCompra->cotizacion->id_transaccion)
            ->where("cantidad_asignada","=",$this->cantidad)
            ;
    }

    public function scopeDisponibleEntradaAlmacen($query)
    {
        return $query->whereHas('entrega', function ($qu) {
            return $qu->whereRaw('ROUND(cantidad, 3) - ROUND(surtida, 3) > 0');
        });
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
     * Este método implementa la lógica del procedimiento almacenado sp_amortizar_antiipo
     * y se detona al registrar una orden de pago para el anticipo de una orden de compra
     */
    public function amortizaAnticipo($importe_pagado)
    {
        try {
            $items_entradas = $this->items_entrada()
                ->where("saldo", ">", 0)
                ->where("anticipo", ">", 0)
                ->orderBy("id_transaccion")
                ->get();
            foreach ($items_entradas as $item_entrada) {
                if ($importe_pagado > 0) {
                    $pagado_remision = round($item_entrada->itemsFactura->sum("importe") - $item_entrada->itemsFactura->sum("saldo"), 2);
                    $monto_pagado = $item_entrada->importe - $item_entrada->saldo;
                    $monto_anticipo = round(($item_entrada->cantidad_original1 * $item_entrada->precio_unitario * $item_entrada->anticipo) / 100, 2);

                    $saldo_remision = $monto_anticipo - ($monto_pagado - $pagado_remision);

                    if ($saldo_remision > 0) {
                        if ($importe_pagado < $saldo_remision) {
                            $pagado_remision = $importe_pagado;
                        } else {
                            $pagado_remision = $saldo_remision;
                        }

                        if($item_entrada->movimiento) {
                            $item_entrada->movimiento->monto_pagado = $item_entrada->movimiento->monto_pagado + $pagado_remision;
                            $item_entrada->movimiento->save();
                        }

                        if ($item_entrada->inventario) {
                            $item_entrada->inventario->monto_pagado = $item_entrada->inventario->monto_pagado + $pagado_remision;
                            $item_entrada->inventario->save();
                            $item_entrada->inventario->distribuirPagoInventarios();
                        }

                        $item_entrada->saldo = $item_entrada->saldo - $pagado_remision;
                        $item_entrada->save();

                        if ($this->ordenCompra->opciones > 65535) {
                            $this->importe = $this->importe + $pagado_remision;
                            $this->save();
                        } else {
                            $this->saldo = $this->saldo - $pagado_remision;
                            $this->save();
                        }
                        $importe_pagado -=$pagado_remision;
                    }
                }
            }
        } catch (\Exception $e){
            abort(500,"Error al amortizar anticipo de item: ".$this->id_item." ".$e->getMessage());
        }
    }
}
