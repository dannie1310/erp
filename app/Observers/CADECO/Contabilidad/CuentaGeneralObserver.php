<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 07:15 PM
 */

namespace App\Observers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CuentaGeneral;

class CuentaGeneralObserver
{
    /**
     * @param CuentaGeneral $cuentaGeneral
     */
    public function creating(CuentaGeneral $cuentaGeneral)
    {
        $cuentaGeneral->estatus = 1;
    }
}