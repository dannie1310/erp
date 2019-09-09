<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 06:47 PM
 */

namespace App\Observers\CADECO\Contabilidad;


use App\Facades\Context;
use App\Models\CADECO\Contabilidad\CuentaBanco;

class CuentaBancoObserver
{
    /**
     * @param CuentaBanco $cuentaBanco
     */
    public function creating(CuentaBanco $cuentaBanco)
    {
        $cuentaBanco->estatus = 1;
        $cuentaBanco->registro = auth()->id();
        $cuentaBanco->id_obra = Context::getIdObra();
    }
}