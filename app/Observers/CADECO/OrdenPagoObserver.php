<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 04:43 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\OrdenPago;
use App\Models\CADECO\Transaccion;

class OrdenPagoObserver extends TransaccionObserver
{
    /**
     * @param OrdenPago $ordenPago
     */
    public function creating(Transaccion $ordenPago)
    {
        parent::creating($ordenPago);
        $ordenPago->numero_folio = $ordenPago->calcularFolio();
        $ordenPago->tipo_transaccion = 68;
        $ordenPago->opciones = 0;
    }

    public function deleted(OrdenPago $ordenPago)
    {
        $ordenPago->regresarSaldo();
    }
}
