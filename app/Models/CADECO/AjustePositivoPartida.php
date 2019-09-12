<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 12/09/2019
 * Time: 12:48 PM
 */

namespace App\Models\CADECO;


class AjustePositivoPartida extends Item
{
    protected $fillable = [
        'id_transaccion',
        'item_antecedente',
        'cantidad',
        'importe',
        'id_almacen',
        'id_material',
        'referencia'
    ];

    public function registrar($partidas, $id_almacen, $ajuste)
    {
        foreach ($partidas as $partida){
            $cantidad_total = $partida['cantidad'];
            $inventarios = Inventario::query()->where('id_material', '=', $partida['id_material']['id'])
                ->where('id_almacen', '=', $id_almacen)
                ->whereRaw('inventarios.saldo != inventarios.cantidad')
                ->orderBy('id_lote', 'desc')->get();
            foreach ($inventarios as $inventario){
                $disponible = $inventario->cantidad - $inventario->saldo;
                if($cantidad_total > 0) {
                    if($cantidad_total > $disponible) {
                        $inventario->saldo = $inventario->saldo + $disponible;
                        $inventario->save();
                        $data = [
                                'id_transaccion' => $ajuste,
                                'item_antecedente' => $inventario->id_lote,
                                'id_almacen' => $id_almacen,
                                'id_material' => $inventario->id_material,
                                'cantidad' => $disponible,
                                'importe' => $partida['monto_pagado'],
                                'referencia' => $partida['id_material']['unidad']
                            ];
                        $registro_partida = $this->create($data);
                        $cantidad_total -= $disponible;
                    } else{
                        $inventario->saldo = $cantidad_total;
                        $inventario->save();
                        $data = [
                            'id_transaccion' => $ajuste,
                            'item_antecedente' => $inventario->id_lote,
                            'id_almacen' => $id_almacen,
                            'id_material' => $inventario->id_material,
                            'cantidad' => $cantidad_total,
                            'importe' => $partida['monto_pagado'],
                            'referencia' => $partida['id_material']['unidad']
                        ];
                        $registro_partida = $this->create($data);
                        $cantidad_total -= $cantidad_total;
                    }
                }else{
                    break;
                }
            }
        }
    }
}