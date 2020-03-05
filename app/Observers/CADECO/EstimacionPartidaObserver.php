<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/11/2019
 * Time: 01:55 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\ItemEstimacion;

class EstimacionPartidaObserver
{
    /**
     * @param ItemEstimacion $partida
     */
    public function creating(ItemEstimacion $partida)
    {
        $partida->validarCantidadesPartidas();
    }

    public function updating(ItemEstimacion $partida)
    {
        $partida->validarCantidadesPartidas();
    }

    public function deleting(ItemEstimacion $partida)
    {
        if($partida->estimacionPartidaEliminada == null)
        {
            abort(400, "Error al eliminar, respaldo incorrecto.");
        }
    }
}
