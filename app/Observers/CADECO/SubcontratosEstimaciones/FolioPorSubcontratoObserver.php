<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 08:55 PM
 */

namespace App\Observers\CADECO\SubcontratosEstimaciones;


use App\Facades\Context;
use App\Models\CADECO\SubcontratosEstimaciones\FolioPorSubcontrato;

class FolioPorSubcontratoObserver
{
    /**
     * @param FolioPorSubcontrato $folioPorSubcontrato
     */
    public function creating(FolioPorSubcontrato $folioPorSubcontrato)
    {
        if (!is_null(Context::getIdObra())) {
            $folioPorSubcontrato->IDObra = Context::getIdObra();
        }
    }
}
