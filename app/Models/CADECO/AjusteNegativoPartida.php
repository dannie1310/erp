<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/09/2019
 * Time: 12:46 PM
 */

namespace App\Models\CADECO;


class AjusteNegativoPartida extends Item
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

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'item_antecedente', 'id_lote');
    }

    public function registrar($partidas, $id_almacen, $ajuste)
    {
        foreach ($partidas as $partida){
            $cantidad_total = $partida['cantidad'];
            $inventarios = Inventario::query()->where('id_material', '=', $partida['id_material']['id'])
                ->where('id_almacen', '=', $id_almacen)
                ->orderBy('id_lote', 'desc')->get();

            foreach ($inventarios as $inventario){
                if($inventario->saldo > 0 && $cantidad_total > 0) {
                    if($cantidad_total >  $inventario->saldo) {
                        $data = [
                            'id_transaccion' => $ajuste,
                            'item_antecedente' => $inventario->id_lote,
                            'id_almacen' => $id_almacen,
                            'id_material' => $inventario->id_material,
                            'cantidad' => $inventario->saldo,
                            'importe' => ($inventario->monto_total/$inventario->cantidad)*($inventario->saldo),
                            'referencia' => $partida['id_material']['unidad']
                        ];
                        $registro_partida = $this->create($data);
                        $inventario->saldo = 0;
                        $inventario->save();
                        $cantidad_total -= $inventario->saldo;
                    } else{
                        $data = [
                            'id_transaccion' => $ajuste,
                            'item_antecedente' => $inventario->id_lote,
                            'id_almacen' => $id_almacen,
                            'id_material' => $inventario->id_material,
                            'cantidad' => $cantidad_total,
                            'importe' => ($inventario->monto_total/$inventario->cantidad)*($cantidad_total),
                            'referencia' => $partida['id_material']['unidad']
                        ];
                        $registro_partida = $this->create($data);
                        $inventario->saldo =  $inventario->saldo - $cantidad_total;
                        $inventario->save();
                        $cantidad_total -= $cantidad_total;
                    }
                }else{
                    break;
                }
            }
        }
    }
}