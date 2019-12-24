<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 24/12/2019
 * Time: 11:03 AM
 */

namespace App\Observers\CADECO\ventas;

class VentaCancelacionObserver {
    /**
     * @param VentaCancelacion $venta_cancelada
     * @throws \Exception
     */
    public function creating(VentaCancelacion $venta_cancelada)
    {
        $venta_cancelada->id_usuario_cancelo = auth()->id();

    }
}