<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 09:55 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\Banco;
use App\Models\CADECO\Empresa;

class BancoObserver extends EmpresaObserver
{
    /**
     * @param Banco $banco
     */
    public function creating(Empresa $banco)
    {
        parent::creating($banco);
        $banco->tipo_empresa = 8;
    }
}