<?php


namespace App\Observers\CADECO;
use App\Models\CADECO\CotizacionCompra;
use App\Models\CADECO\Transaccion;


class CotizacionCompraObserver extends TransaccionObserver
{
    /**
     * @param CotizacionCompra $cotizacionCompra
     */

    public function creating(Transaccion $cotizacionCompra)
    {
        parent::creating($cotizacionCompra);

        $cotizacionCompra->tipo_transaccion = 18;
        $cotizacionCompra->estado = 1;
        $cotizacionCompra->opciones = 1;
        $cotizacionCompra->id_moneda = 1;
        
    }
}