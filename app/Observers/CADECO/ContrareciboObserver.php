<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 31/12/2019
 * Time: 11:00 AM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\ContraRecibo;
use App\Models\CADECO\Transaccion;

class ContrareciboObserver  extends TransaccionObserver
{
    /**
     * @param Transaccion $pago
     * @throws \Exception
     */
    public function creating(Transaccion $contrarecibo)
    {
        parent::creating($contrarecibo);
        $contrarecibo->tipo_transaccion = 67;
        $contrarecibo->opciones = 0;
    }

    public function updating(ContraRecibo $contrarecibo){
        if($contrarecibo->saldo<-1)
        {
            throw New \Exception('El saldo del contrarecibo '.$contrarecibo->numero_folio_format.' no puede ser menor a 0. Saldo Resultante: '.$contrarecibo->saldo_format.' ');
        }
    }

}