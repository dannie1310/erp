<?php


namespace App\Observers\CADECO\Compras;

use App\Models\CADECO\Compras\CotizacionComplemento;

class CotizacionComplementoObserver
{
    /**
     * @param CotizacionComplemeto $CotizacionComplemento
     */

     public function creating(CotizacionComplemento $cotizacionComplemento)
     {
         dd('cotizacionComplemento');
     }
}