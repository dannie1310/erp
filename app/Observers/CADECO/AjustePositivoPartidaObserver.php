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
     * @throws \Exception
     */
    public function creating(AjustePositivoPartida $partida)
    {
        $partida->esado = 0;
    }
}