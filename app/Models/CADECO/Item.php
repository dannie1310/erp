<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 11/03/2019
 * Time: 07:15 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'items';
    protected $primaryKey = 'id_item';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_antecedente',
        'item_antecedente',
        'id_concepto',
        'id_material',
        'unidad',
        'cantidad',
        'cantidad_material',
        'cantidad_mano_obra',
        'importe',
        'saldo',
        'anticipo',
        'descuento',
        'precio_unitario',
        'precio_material',
        'precio_mano_obra',
    ];

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen', 'id_almacen');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }

    public function partida_antecedente()
    {
        return $this->belongsTo(self::class, 'item_antecedente', 'id_item');
    }

    public function movimiento()
    {
        return $this->belongsTo(Movimiento::class, 'id_item', 'id_item');
    }

    public function transaccion()
    {
        return $this->belongsTo(Transaccion::class, 'id_transaccion', 'id_transaccion');
    }

    public function transaccionAntecedente()
    {
        return $this->belongsTo(Transaccion::class, 'id_antecedente', 'id_transaccion');
    }

    public function getCantidadFormatAttribute()
    {
        return number_format($this->cantidad,3,'.', ',');

    }
    public function getCantidadDecimalAttribute()
    {
        return round($this->cantidad,3);

    }

    public function getImporteFormatAttribute(){
        return '$ ' .  number_format($this->importe,2,'.', ',');
    }

    public function getSaldoFormatAttribute(){
        return '$ ' . number_format($this->saldo,2,'.', ',');
    }

    public function getPrecioUnitarioFormatAttribute(){
        return '$ ' . number_format($this->precio_unitario,2,'.', ',');
    }

    public function getPrecioMaterialFormatAttribute(){
        return '$ ' . number_format($this->precio_material,2,'.', ',');
    }

    public function getDescuentoFormatAttribute(){
        return number_format($this->descuento,2,'.', '');
    }

    /**
     * Este método implementa la lógica del stored procedure: sp_entradas_salidas y se invoca al crear un nuevo
     * inventario ya sea por entrada de almacén  o por transferencia
     **/
    public function ajustarValoresConsumos()
    {
        $inventarios_consumo = Inventario::where("id_almacen", "=", $this->id_almacen)
            ->where("id_material", "=", $this->id_material)
            ->where("saldo", "<", 0)
            ->orderBy("id_lote")
            ->get();
        $inventarios_existencia = Inventario::where("id_almacen", "=", $this->id_almacen)
            ->where("id_material", "=", $this->id_material)
            ->where("saldo", ">", 0)
            ->orderBy("id_lote")
            ->get();

        $consumo = 0;
        $existencia = 0;
        $id_lote = 0;
        $id_item = 0;
        $cantidad = 0;
        $monto_total = 0;
        $monto_pagado = 0;
        $i = 0;
        while (1 > 0) {
            if ($consumo == 0) {
                if (!isset($inventarios_consumo[$i])) {
                    break;
                }
                $id_item = $inventarios_consumo[$i]->id_item;
                $consumo = $inventarios_consumo[$i]->saldo * (-1);

            }
            if ($existencia == 0) {
                if (!isset($inventarios_existencia[$i])) {
                    break;
                }
                $id_lote = $inventarios_existencia[$i]->id_lote;
                $existencia = $inventarios_existencia[$i]->saldo;
                $cantidad = $inventarios_existencia[$i]->cantidad;
                $monto_total = $inventarios_existencia[$i]->monto_total;
                $monto_pagado = $inventarios_existencia[$i]->monto_pagado;
            }
            if ($existencia >= $consumo) {
                /*Se descuenta el saldo de la transferencia*/
                $inventarios_existencia[$i]->saldo = $inventarios_existencia[$i]->saldo - $consumo;
                $inventarios_existencia[$i]->monto_aplicado = $inventarios_existencia[$i]->monto_aplicado +
                    round($monto_total*$consumo/$cantidad,2);
                $inventarios_existencia[$i]->save();
                /*Se actualiza la información del consumo*/
                $inventarios_consumo[$i]->monto_total = $inventarios_consumo[$i]->monto_total +
                    round($monto_total * $consumo / $cantidad,2);
                $inventarios_consumo[$i]->saldo = 0;
                $inventarios_consumo[$i]->lote_antecedente = $id_lote;
                $inventarios_consumo[$i]->save();
                $movimiento = Movimiento::where("id_item","=",$id_item)
                    ->where("id_material","=",$this->id_material)->find();
                $movimiento->monto_total = $movimiento->monto_total +
                    round($monto_total*$consumo/$cantidad,2);
                $movimiento->save();
                $existencia = $existencia - $consumo;
                $consumo = 0;
                /*dd($movimiento);
                break;*/
            } else {
                /*Descontamos la salida*/
                $inventarios_existencia[$i]->saldo = 0;
                $inventarios_existencia[$i]->monto_aplicado = $inventarios_existencia[$i]->monto_aplicado +
                    round($monto_pagado * $existencia/$cantidad,2);
                $inventarios_existencia[$i]->save();
                /*Cancelamos la salida*/
                $inventario = Inventario::create([
                    "id_almacen" => $this->id_almacen,
                    "id_material" => $this->id_material,
                    "lote_antecedente" => $id_lote,
                    "id_item" =>$id_item,
                    "cantidad" => $existencia * (-1),
                    "monto_total" => round($monto_total *$existencia/$cantidad,2),
                    "monto_pagado" => round($monto_pagado *$existencia/$cantidad,2),
                ]);

                /*La actualización de saldo se hace con el mass update para evitar la validación del observer*/
                Inventario::where("id_lote","=",$inventarios_consumo[$i]->id_lote)
                    ->update([
                        "cantidad"=>$inventarios_consumo[$i]->saldo + $existencia,
                        "saldo" => $inventarios_consumo[$i]->saldo + $existencia
                    ]);

                $movimiento = Movimiento::where("id_item","=",$id_item)
                    ->where("id_material","=",$this->id_material)->first();
                if($movimiento){
                    $movimiento->monto_total = $movimiento->monto_total +
                        round($monto_total*$existencia/$cantidad,2);
                    $movimiento->monto_pagado = $movimiento->monto_pagado +
                        round($monto_pagado*$existencia/$cantidad,2);
                    $movimiento->save();
                }
                $consumo = $consumo - $existencia;
                $existencia = 0;
            }
            $i++;
        }
    }
}
