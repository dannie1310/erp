<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/11/2019
 * Time: 01:55 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\EstimacionPartida;

class EstimacionPartidaObserver
{
    /**
     * @param EstimacionPartida $partida
     */
    public function deleting(EstimacionPartida $partida)
    {
        if($partida->estimacionPartidaEliminada == null)
        {
            abort(400, "Error al eliminar, respaldo incorrecto.");
        }
    }
}