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
     * @throws \Exception
     *
     */
    public function creating(Entrega $entrega)
    {
        $entrega->numero_entrega = 1;
        $entrega->fecha = date('Y-m-d');
    }

    public function updating(Entrega $entrega)
    {
        if($entrega->surtida > ($entrega->cantidad+0.01))
        {
            throw New \Exception('La cantidad surtida sobrepasa la cantidad solicitada.');
        }
    }
}
