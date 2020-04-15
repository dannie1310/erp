<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 05/11/2019
 * Time: 01:03 p. m.
 */


namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\SolicitudCompra;
use App\Models\CADECO\Transaccion;

class SolicitudCompraObserver extends TransaccionObserver
{
    /**
     * @param SolicitudCompra $solicitudCompra
     */

    public function creating(Transaccion $solicitudCompra)
    {
        parent::creating($solicitudCompra);

        $solicitudCompra->tipo_transaccion = 17;
        $solicitudCompra->estado = 0;
        $solicitudCompra->opciones = 1;
    }


}
