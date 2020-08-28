<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 04:50 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Pago;
use App\Models\CADECO\PagoVario;
use App\Models\CADECO\Transaccion;

class PagoVarioObserver extends PagoObserver
{
    /**
     * @param PagoVario $pagoVario
     * @throws \Exception
     */
    public function creating(Transaccion $pagoVario)
    {
        parent::creating($pagoVario);
        $pagoVario->tipo_transaccion = 82;
        $pagoVario->opciones = 1;
    }

    public function deleting(Pago $pagoVario)
    {
        parent::deleting($pagoVario);
    }
}
