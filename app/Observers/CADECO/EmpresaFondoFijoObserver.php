<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 10:13 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\EmpresaFondoFijo;

class EmpresaFondoFijoObserver
{
    /**
     * @param EmpresaFondoFijo $empresa
     */
    public function creating(EmpresaFondoFijo $empresa)
    {
        $empresa->tipo_empresa = 32;
        $empresa->razon_social= mb_strtoupper($empresa->razon_social);
        $empresa->UsuarioRegistro = auth()->id();
        $empresa->FechaHoraRegistro = date('Y-m-d h:i:s');
    }
}