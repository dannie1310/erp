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
use App\Models\CADECO\Pago;

class PagoReposicionFFObserver extends PagoObserver
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

    public function created(Pago $pago)
    {
        parent::created($pago);
        $pago->fondo->aumentaSaldo($pago);
        if(abs(abs($pago->monto*(1/$pago->tipo_cambio))-$pago->solicitud->monto)>0.99){
            $pago->solicitud->generaSolicitudComplemento();
        }
        $pago->solicitud->actualizaEstadoPagada();
    }

    public function deleting(Pago $pago)
    {
        parent::deleting($pago);
    }
}
