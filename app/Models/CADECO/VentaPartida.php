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
    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'id_item', 'id_item');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_transaccion', 'id_transaccion');
    }

    public function getImporteFormatAttribute()
    {
        return '$ ' . number_format($this->importe,2, '.', ',');
    }

    public function getPrecioUnitarioFormatAttribute()
    {
        return '$ ' . number_format($this->precio_unitario, 2, '.', ',');
    }

    /**
     *  Este método realiza la salida de un material en el inventario, detonando un movimiento asociado a un inventario
     *  de origen.
     */
    public function ventaMaterial()
    {
        $inventario_existencia = Inventario::where("id_material", "=", $this->id_material)
            ->where("saldo", ">", 0)
            ->orderBy("id_lote")
            ->get();
        $cantidad = $this->cantidad;
        $importe = 0;

        foreach ($inventario_existencia as $inventario)
        {
            if($cantidad >= $inventario->saldo)
            {
                $this->movimiento()->create([
                    'id_concepto' => $this->id->id_concepto,
                    'id_item' => $this->id_item,
                    'id_material' => $this->id_material,
                    'lote_antecedente' => $inventario->id_lote,
                    'cantidad' => $inventario->saldo,
                    'monto_total' => round($inventario->monto_total * $inventario->saldo / $inventario->cantidad, 2),
                    'monto_pagado' => round($inventario->monto_pagado * $inventario->saldo / $inventario->cantidad, 2),

                ]);
                $cantidad = $cantidad - $inventario->saldo;
                $importe = round($importe + ($inventario->monto_total * $inventario->saldo / $inventario->cantidad), 2);
                $inventario->disminuyeSaldo($inventario->saldo);
            }else{
                Movimiento::create([
                    'id_concepto' => $this->id_concepto,
                    'id_item' => $this->id_item,
                    'id_material' => $this->id_material,
                    'lote_antecedente' => $inventario->id_lote,
                    'cantidad' => $cantidad,
                    'monto_total' => round($inventario->monto_total * $cantidad / $inventario->cantidad, 2),
                    'monto_pagado' => round($inventario->monto_pagado * $cantidad / $inventario->cantidad, 2),
                ]);
                $inventario->disminuyeSaldo($cantidad);
                $importe = round($importe + ($inventario->monto_total * $cantidad / $inventario->cantidad), 2);
                $cantidad = 0;
            }
            $inventario->distribuirPagoInventarios();
            if ($cantidad == 0) {
                break;
            }
        }
        if ($cantidad > 0) {
            abort(400, 'Saldo insuficiente para: '.$this->material->descripcion.', faltante: '.$cantidad);
        }

        $item = VentaPartida::find($this->id_item);
        $item->update(["importe" => $importe]);
    }

    public function eliminarMovimientos(){
        foreach($this->movimientos as $movimiento){
            $movimiento->delete();
        }
    }
}