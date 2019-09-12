<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 07:18 PM
 */

namespace App\Observers\CADECO\Contabilidad;


use App\Facades\Context;
use App\Models\CADECO\Contabilidad\CuentaMaterial;

class CuentaMaterialObserver
{
    /**
     * @param CuentaMaterial $cuentaMaterial
     */
    public function creating(CuentaMaterial $cuentaMaterial)
    {
        $cuentaMaterial->estatus = 1;
        $cuentaMaterial->registro = auth()->id();
        $cuentaMaterial->id_obra = Context::getIdObra();
    }
}