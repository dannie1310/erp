<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 06:42 PM
 */

namespace App\Observers\CADECO\Contabilidad;


use App\Facades\Context;
use App\Models\CADECO\Contabilidad\Cierre;

class CierreObserver
{
    /**
     * @param Cierre $cierre
     */
    public function creating(Cierre $cierre)
    {
        $cierre->id_obra = Context::getIdObra();
        $cierre->registro = auth()->id();
    }
}