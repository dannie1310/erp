<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 04:46 PM
 */

namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Pago;
use App\Models\CADECO\Transaccion;

class PagoObserver extends TransaccionObserver
{
    /**
     * @param Pago $pago
     * @throws \Exception
     */
    public function creating(Transaccion $pago)
    {
        parent::creating($pago);
        $pago->tipo_transaccion = 82;
        $pago->estado = 1;
    }

    public function created(Pago $pago){
        $pago->cuenta->disminuyeSaldo($pago);
    }

    public function deleting(Pago $pago)
    {
        if(is_null($pago->pagoEliminadoRespaldo))
        {
            abort(400, "Error al respaldar el pago a eliminar");
        }
        $pago->desvincularPolizas();
    }
}
