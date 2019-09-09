<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 06:45 PM
 */

namespace App\Observers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CuentaAlmacen;

class CuentaAlmacenObserver
{
    /**
     * @param CuentaAlmacen $cuentaAlmacen
     */
    public function creating(CuentaAlmacen $cuentaAlmacen)
    {
        $cuentaAlmacen->registro = auth()->id();
        $cuentaAlmacen->estatus = 1;
    }
}