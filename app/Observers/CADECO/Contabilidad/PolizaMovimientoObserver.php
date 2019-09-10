<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 07:24 PM
 */

namespace App\Observers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\PolizaMovimiento;

class PolizaMovimientoObserver
{
    /**
     * @param PolizaMovimiento $polizaMovimiento
     */
    public function creating(PolizaMovimiento $polizaMovimiento)
    {
        $polizaMovimiento->estatus = 1;
    }
}