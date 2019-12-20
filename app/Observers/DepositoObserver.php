<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/12/2019
 * Time: 09:32 PM
 */

namespace App\Observers;


use App\Models\CADECO\Deposito;
use App\Models\CADECO\Transaccion;
use App\Observers\CADECO\TransaccionObserver;

class DepositoObserver extends TransaccionObserver
{
    /**
     * @param Deposito $deposito
     * @throws \Exception
     */
    public function creating(Transaccion $deposito)
    {
        parent::creating($deposito);
        $deposito->tipo_transaccion = 81;
        $deposito->estado = 1;
    }
}