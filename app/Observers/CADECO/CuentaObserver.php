<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 10:01 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Cuenta;

class CuentaObserver
{
    /**
     * @param Cuenta $cuenta
     * @throws \Exception
     */
    public function creating(Cuenta $cuenta)
    {
        if(!cuenta::query()->where('numero', '=',  $cuenta->numero)->first()) {
            $cuenta->saldo_real = $cuenta->saldo_inicial;
            $cuenta->fecha_real = $cuenta->fecha_inicial;
            $cuenta->fecha_estado = $cuenta->fecha_inicial;
            $cuenta->estado = 0;
        }else {
            throw New \Exception('Ya existe un registro con el mismo número de cuenta.');
        }
    }

    public function created(Cuenta $cuenta)
    {
        $cuenta->cuentasObra()->create(['id_obra'=>Context::getIdObra(), 'id_cuenta'=> $cuenta->id_cuenta]);
    }
}