<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 06:56 PM
 */

namespace App\Observers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CuentaCosto;

class CuentaCostoObserver
{
    /**
     * @param CuentaCosto $cuentaCosto
     */
    public function creating(CuentaCosto $cuentaCosto)
    {
        $cuentaCosto->registro = auth()->id();
        $cuentaCosto->estatus = 1;
    }
}