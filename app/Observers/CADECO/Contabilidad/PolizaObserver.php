<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 07:22 PM
 */

namespace App\Observers\CADECO\Contabilidad;


use App\Facades\Context;
use App\Models\CADECO\Contabilidad\Poliza;

class PolizaObserver
{
    /**
     * @param Poliza $poliza
     */
    public function creating(Poliza $poliza)
    {
        $poliza->id_obra_cadeco = Context::getIdObra();
    }
}