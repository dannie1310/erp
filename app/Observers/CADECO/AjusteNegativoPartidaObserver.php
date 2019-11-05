<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/09/2019
 * Time: 06:11 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\AjusteNegativoPartida;

class AjusteNegativoPartidaObserver
{
    /**
     * @param AjusteNegativoPartida $partida
     */
    public function creating(AjusteNegativoPartida $partida)
    {
        $partida->estado = 0;
    }

    /**
     * @param AjusteNegativoPartida $partida
     */
    public function created(AjusteNegativoPartida $partida){
        $partida->inventario->saldo = $partida->inventario->saldo - $partida->cantidad;
        $partida->inventario->save();
    }
}