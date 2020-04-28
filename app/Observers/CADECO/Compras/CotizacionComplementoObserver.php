<?php


namespace App\Observers\CADECO\Compras;

use App\Models\CADECO\Compras\CotizacionComplemento;

class CotizacionComplementoObserver
{
    /**
     * @param CotizacionComplemento $cotizacionComplemento
     */

     public function creating(CotizacionComplemento $cotizacionComplemento)
     {
         $cotizacionComplemento->registro = auth()->id();
     }
}