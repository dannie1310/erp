<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 27/11/2019
 * Time: 07:56 PM
 */

namespace App\Observers\CADECO\Compras;


use App\Models\CADECO\Compras\RequisicionPartidaEliminada;
use App\Models\CADECO\RequisicionPartida;

class RequisicionPartidaObserver
{
    /**
     * @param RequisicionPartida $partida
     */
    public function deleted(RequisicionPartida $partida)
    {
      dd($partida);
        RequisicionPartidaEliminada::query()->create(
            [
                'id_item' => $partida->id_item,
                'id_transaccion' => $partida->id_transaccion,
                'id_material' => $partida->id_material,
                'unidad' => $partida->complemento->unidad,
                'cantidad' => $partida->cantidad,
                'descripcion_material' => $partida->complemento->descripcion_material,
                'numero_parte' => $partida->complemento->numero_parte,
                'observaciones' => $partida->complemento->observaciones,
                'fecha_entrega' => $partida->complemento->fecha_entrega,
                'usuario_registo' => $partida->complemento->usuario_registo,
                'timestamp_registro' => $partida->complemento->timestamp_registro
            ]
        );
    }
}