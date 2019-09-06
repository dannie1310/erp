<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 06:49 PM
 */

namespace App\Observers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CuentaConcepto;

class CuentaConceptoObserver
{
    /**
     * @param CuentaConcepto $cuentaConcepto
     */
    public function creating(CuentaConcepto $cuentaConcepto)
    {
        $cuentaConcepto->registro = auth()->id();
        $cuentaConcepto->estatus = 1;
    }
}