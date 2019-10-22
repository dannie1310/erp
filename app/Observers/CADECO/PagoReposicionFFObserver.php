<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 04:46 PM
 */

namespace App\Observers\CADECO;
use App\Models\CADECO\PagoReposicionFF;
use App\Models\CADECO\Transaccion;

class PagoReposicionFFObserver extends TransaccionObserver
{
    /**
     * @param PagoReposicionFF $pago
     * @throws \Exception
     */
    public function creating(Transaccion $pago)
    {
        parent::creating($pago);
        $pago->tipo_transaccion = 82;
        $pago->opciones = 1;
    }

    public function created(PagoReposicionFF $pago)
    {
        $pago->fondo->aumentaSaldo($pago);
        $pago->cuenta->disminuyeSaldo($pago);
        $pago->solicitud->actualizaEstadoPagada();
    }
}
