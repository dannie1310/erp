<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 07:26 PM
 */

namespace App\Observers\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\TipoCuentaContable;

class TipoCuentaContableObserver
{
    /**
     * @param TipoCuentaContable $cuentaContable
     */
    public function creating(TipoCuentaContable $cuentaContable)
    {
        $cuentaContable->tipo = 1;
        $cuentaContable->registro = auth()->id();
    }
}