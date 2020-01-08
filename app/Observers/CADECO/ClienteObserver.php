<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 07/01/2020
 * Time: 07:01 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\Cliente;
use App\Models\CADECO\Empresa;

class ClienteObserver extends EmpresaObserver
{
    /**
     * @param Cliente $cliente
     */
    public function creating(Empresa $cliente)
    {
        parent::creating($cliente);
        $cliente->tipo_empresa = 16;
    }
}