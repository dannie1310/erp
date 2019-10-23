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

class PagoAnticipoDestajoObserver extends PagoObserver
{
    /**
     * @param PagoReposicionFF $pago
     * @throws \Exception
     */
    public function creating(Transaccion $pago)
    {
        parent::creating($pago);
        $pago->tipo_transaccion = 82;
        $pago->opciones = 131073;
        $pago->observaciones = 'Anticipo a Proveedores / Destajistas';
    }

    public function created(Pago $pago)    {
        parent::created($pago);
        $pago->solicitud->actualizaEstadoPagada();
        $pago->generaAnticipo();
    }
}
