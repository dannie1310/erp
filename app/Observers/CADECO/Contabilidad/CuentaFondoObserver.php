<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 07:03 PM
 */

namespace App\Observers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CuentaFondo;

class CuentaFondoObserver
{
    /**
     * @param CuentaFondo $cuentaFondo
     */
    public function creating(CuentaFondo $cuentaFondo)
    {
        $cuentaFondo->registro = auth()->id();
        $cuentaFondo->estatus = 1;
    }
}