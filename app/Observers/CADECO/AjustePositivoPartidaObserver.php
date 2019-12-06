<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 12/09/2019
 * Time: 04:11 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\AjustePositivoPartida;

class AjustePositivoPartidaObserver
{
    /**
     * @param AjustePositivoPartida $partida
     */
    public function creating(AjustePositivoPartida $partida)
    {
        $partida->estado = 0;
    }

    /**
     * @param AjustePositivoPartida $partida
     */
    public function created(AjustePositivoPartida $partida){
        $partida->inventario->aumentaSaldo($partida->cantidad);
    }

    public function deleted(AjustePositivoPartida $partida){
        $partida->inventario->disminuyeSaldo($partida->cantidad);
    }
}