<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 06:23 PM
 */

namespace App\Observers\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaPartida;

class DistribucionRecursoRemesaPartidaObserver
{
    /**
     * @param DistribucionRecursoRemesaPartida $partida
     * @throws \Exception
     */
    public function creating(DistribucionRecursoRemesaPartida $partida)
    {
        if(DistribucionRecursoRemesaPartida::query()->where('id_documento', '=',  $partida->id_documento)->where('estado', '>=', 0)->get()->toArray() == []) {
            $partida->fecha_registro = date('Y-m-d H:i:s');
            $partida->estado = 0;
        }else {
            throw New \Exception('Está distribución no puede ser procesada debido a que cuenta con documentos relacionados en otra distribución.');
        }
    }
}