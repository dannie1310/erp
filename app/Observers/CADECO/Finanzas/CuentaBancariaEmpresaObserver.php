<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 03/09/2019
 * Time: 08:30 PM
 */

namespace App\Observers\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\CuentaBancariaEmpresa;

class CuentaBancariaEmpresaObserver
{
    /**
     * @param CuentaBancariaEmpresa $cuenta
     */

    public function creating(CuentaBancariaEmpresa $cuenta)
    {
        $cuenta->validar();
        $cuenta->fecha_hora_registro = date('Y-m-d h:i:s');
        $cuenta->registro = auth()->id();
    }
}