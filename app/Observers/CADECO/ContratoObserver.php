<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 29/10/2019
 * Time: 04:16 PM
 */

namespace App\Observers\CADECO;

use App\Models\CADECO\Contrato;


class ContratoObserver
{
    /**
     * @param Contrato $contrato
     * @throws \Exception
     *
     */
    public function created(Contrato $contrato)
    {
        $contrato->registrarDestino();
    }

}