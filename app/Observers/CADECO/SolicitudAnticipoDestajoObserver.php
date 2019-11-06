<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 29/10/2019
 * Time: 09:25 PM
 */

namespace App\Observers\CADECO;
use App\Models\CADECO\Transaccion;

class SolicitudAnticipoDestajoObserver extends TransaccionObserver
{
    /**
     * @param Transaccion $solicitud
     * @throws \Exception
     */
    public function creating(Transaccion $solicitud)
    {
        parent::creating($solicitud);
        $solicitud->tipo_transaccion = 72;
        $solicitud->opciones = 131073;
        $solicitud->estado = 0;
    }
}