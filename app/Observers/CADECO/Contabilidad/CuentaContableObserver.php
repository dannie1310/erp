<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 06:52 PM
 */

namespace App\Observers\CADECO\Contabilidad;


use App\Facades\Context;
use App\Models\CADECO\Contabilidad\CuentaContable;

class CuentaContableObserver
{
    /**
     * @param CuentaContable $cuentaContable
     */
    public function creating(CuentaContable $cuentaContable)
    {
        $cuentaContable->id_obra = Context::getIdObra();
    }
}