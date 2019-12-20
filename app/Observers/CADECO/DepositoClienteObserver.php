<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/12/2019
 * Time: 09:19 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\Deposito;
use App\Models\CADECO\DepositoCliente;
use App\Observers\DepositoObserver;

class DepositoClienteObserver extends DepositoObserver
{
    /**
     * @param DepositoCliente $deposito
     * @throws \Exception
     */
    public function creating(Deposito $deposito)
    {
        parent::creating($deposito);
        $deposito->opciones = 4;
    }
}