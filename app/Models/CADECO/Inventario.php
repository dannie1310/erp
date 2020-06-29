<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 05:51 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\CADECO\Compras\InventarioEliminado;

class Inventario extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'inventarios';
    protected $primaryKey = 'id_lote';

    public $timestamps = false;

    public $searchable = [
        'id_material'
    ];

    protected $fillable = [
        'id_item',
        'id_almacen',
        'id_material',
        'cantidad',
        'lote_antecedente',
        'saldo',
        'monto_total',
        'monto_original',
        'monto_pagado',
        'monto_anticipo',
    ];

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'lote_antecedente', 'id_lote');
    }

    public function inventarios_hijos()
    {
        return $this->hasMany(Inventario::class, 'lote_antecedente', 'id_lote');
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'lote_antecedente', 'id_lote');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen', 'id_almacen');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }

    public function item()
    {
        return $this->hasMany(Item::class, 'id_item', 'id_item');
    }

    public function items_ajuste()
    {
        return $this->hasMany(ItemAjuste::class, 'item_antecedente', 'id_lote');
    }

    public function getCantidadFormatAttribute()
    {
        return number_format($this->cantidad, 3, '.', '');
    }

    public function getSaldoFormatAttribute()
    {
        return number_format($this->saldo, 3, '.', '');
    }

    public function disminuyeSaldo($cantidad)
    {
        $this->saldo = $this->saldo - $cantidad;
        $this->save();
    }
     /* Este método implementa la lógica del procedimiento almacenado
     * sp_borra_transaccion y se detona al eliminar una salida de almacén
     * */
    public function aumentaSaldo($cantidad)
    {
        $this->saldo = $this->saldo + $cantidad;
        $this->save();
    }
    /* Este método implementa la lógica del procedimiento almacenado
     * sp_borra_transaccion y se detona al eliminar una salida de almacén
     * */
    public function disminuyeMontoAplicado($monto_pagado_hijo)
    {
        $this->monto_aplicado = $this->monto_aplicado - $monto_pagado_hijo;
        $this->save();
    }

    /**
     * Este método implementa la lógica del stored procedure: sp_distribuir_pagado_inventarios y se invoca al generar
     * una salida o un pago
     **/
    public function distribuirPagoInventarios()
    {
        try{
            $tipo_distribucion = null;
            if ($this->cantidad <= 0) {
                throw New \Exception('Cantidad erronea para el lote:' . $this->id_lote);
            }
            if (in_array($this->almacen->tipo_almacen, [0, 3, 4]) || in_array($this->material->tipo_material, [1, 2])) {
                $tipo_distribucion = 1;
                $monto_aplicado = $this->distribucionProporcional();
            } else if (in_array($this->material->tipo_material, [4, 8])) {
                $tipo_distribucion = 2;
                $monto_aplicado = $this->distribucionRentas();
            }
            if ($tipo_distribucion == null) {
                throw New \Exception('No se puede determinar el tipo de distribución del lote:' . $this->id_lote);
            }
            $this->aplicarMontoPorAjustes($monto_aplicado);
        }
        catch (\Exception $e){
            abort(500, "Error en distribución de pago de invetario ".$this->id_lote.": ".$e->getMessage());
        }

    }

    private function distribucionProporcional()
    {
        try{
            $por_aplicar = $this->monto_pagado;
            $monto_aplicado = 0;
            $movimientos = $this->movimientos()->where("cantidad", ">", 0)->get();
            $inventarios = $this->inventarios_hijos;
            foreach ($movimientos as $movimiento) {
                $monto_pagado = round($por_aplicar * $movimiento->cantidad / $this->cantidad, 2);
                $movimiento->monto_pagado = $monto_pagado;
                $movimiento->save();
                $monto_aplicado += $monto_pagado;
            }
            foreach ($inventarios as $inventario) {
                if ($inventario->cantidad > 0) {
                    $monto_pagado = round($por_aplicar * $inventario->cantidad / $this->cantidad, 2);
                    $inventario->monto_pagado = $monto_pagado;
                    $inventario->save();
                    $inventario->distribuirPagoInventarios();
                    $monto_aplicado += $monto_pagado;
                } else {
                    $monto_pagado = round($por_aplicar * (-1) * $inventario->cantidad / $this->cantidad, 2);
                    $inventario->monto_pagado = $monto_pagado;
                    $inventario->save();
                    /*Inferencia de consumo, se actualizan los movimientos*/
                    $movimientos_inferidos = Movimiento::where("id_item", "=", $inventario->id_item)
                        ->where("id_material", "=", $this->id_material)
                        ->get();
                    $inventarios_inferidos = Inventario::where("id_item", "=", $inventario->id_item)
                        ->where("id_material", "=", $this->id_material)
                        ->get();
                    $monto_pagado_inventarios_inferidos = $inventarios_inferidos->sum("monto_pagado");
                    foreach ($movimientos_inferidos as $movimiento_inferido) {
                        $movimiento_inferido->monto_pagado = $monto_pagado_inventarios_inferidos;
                        $movimiento_inferido->save();
                    }
                    $monto_aplicado += $monto_pagado;
                }
            }
            return $monto_aplicado;
        } catch (\Exception $e){
            abort(500, "Error en distribución proporcional: ".$e->getMessage());
        }

    }

    private function distribucionRentas()
    {
        $por_aplicar = $this->monto_pagado - $this->monto_aplicado;
        $monto_aplicado = $this->monto_aplicado;
        if ($por_aplicar >= 0) {
            $movimientos = $this->movimientos()->whereRaw("monto_total >monto_pagado")->orderBy("id_movimiento")->get();
            foreach ($movimientos as $movimiento) {
                $por_pagar = $movimiento->monto_total - $movimiento->monto_pagado;
                if ($por_pagar > $por_aplicar) {
                    $por_pagar = $por_aplicar;
                }
                $movimiento->monto_pagado = $movimiento->monto_pagado + $por_pagar;
                $por_aplicar -= $por_pagar;
                $monto_aplicado += $por_pagar;
                if (!($por_aplicar > 0)) {
                    break;
                }
            }
        } else {
            $movimientos = $this->movimientos()->where("monto_pagado", ">", 0)->orderBy("id_movimiento", "desc")->get();
            foreach ($movimientos as $movimiento) {
                $por_pagar = $movimiento->monto_pagado * (-1);
                if ($por_pagar < $por_aplicar) {
                    $por_pagar = $por_aplicar;
                }
                $movimiento->monto_pagado = $movimiento->monto_pagado + $por_pagar;
                $por_aplicar -= $por_pagar;
                $monto_aplicado += $por_pagar;
                if (!($por_aplicar < 0)) {
                    break;
                }
            }

        }
        return $monto_aplicado;
    }
    private function aplicarMontoPorAjustes($monto_aplicado)
    {
        try{
            $items_ajuste = $this->items_ajuste;
            $monto_aplicado_ia = 0;
            foreach($items_ajuste as $item_ajuste)
            {
                $monto_aplicado_ia += ($item_ajuste->importe - $item_ajuste->saldo);
            }
            $this->monto_aplicado = round($monto_aplicado+$monto_aplicado_ia,2);
            $this->save();
        }
        catch (\Exception $e){
            abort(500, "Error en aplicación de monto por ajuste: ".$e->getMessage());
        }
    }


    private function cambiarMontoPagado($monto_pagado)
    {
        $this->update([
            'monto_pagado' => $monto_pagado
        ]);
        $this->distribuirPagoInventarios();
    }
}
