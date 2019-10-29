<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 29/10/2019
 * Time: 04:16 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\Entrega;

class EntregaObserver
{
    /**
     * @param Entrega $entrega
     */
    public function updating(Entrega $entrega)
    {
        $entrega->surtida = $entrega->getOriginal('surtida') + $entrega->surtida;
        dd("aqui", $entrega->getOriginal('surtida'), $entrega->surtida);
    }

    public function updated(Entrega $entrega)
    {

    }
}