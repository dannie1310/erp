<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 10:13 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\Empresa;
use App\Models\CADECO\EmpresaFondoFijo;

class EmpresaFondoFijoObserver extends EmpresaObserver
{
    /**
     * @param EmpresaFondoFijo $empresa
     */
    public function creating(Empresa $empresa)
    {
        parent::creating($empresa);
        $empresa->tipo_empresa = 32;
    }
}