<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/12/2019
 * Time: 09:19 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\DepositoCliente;
use App\Models\CADECO\Transaccion;

class DepositoClienteObserver extends TransaccionObserver
{
    /**
     * @param DepositoCliente $deposito
     * @throws \Exception
     */
    public function creating(Transaccion $deposito)
    {
        parent::creating($deposito);
        $deposito->tipo_transaccion = 81;
        $deposito->opciones = 4;
        $deposito->estado = 1;
    }

    public function created(Transaccion $deposito){
        $deposito->cuenta->aumentaSaldoPorDeposito($deposito);
    }

    public function deleting(Transaccion $deposito){
        $deposito->cuenta->disminuyeSaldoPorDeposito($deposito);
    }
}