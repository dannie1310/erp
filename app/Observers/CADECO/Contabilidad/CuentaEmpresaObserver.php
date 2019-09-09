<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 07:00 PM
 */

namespace App\Observers\CADECO\Contabilidad;


use App\Facades\Context;
use App\Models\CADECO\Contabilidad\CuentaEmpresa;

class CuentaEmpresaObserver
{
    /**
     * @param CuentaEmpresa $cuentaEmpresa
     */
    public function creating(CuentaEmpresa $cuentaEmpresa)
    {
        $cuentaEmpresa->estatus = 1;
        $cuentaEmpresa->registro = auth()->id();
        $cuentaEmpresa->id_obra = Context::getIdObra();
    }
}