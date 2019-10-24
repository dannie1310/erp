<?php


namespace App\Observers\CADECO;


use App\Facades\Context;
use App\Models\CADECO\AjusteNegativo;
use App\Models\CADECO\NuevoLote;
use App\Models\CADECO\Transaccion;

class NuevoLoteObserver extends TransaccionObserver
{
    /**
     * @param NuevoLote $nuevo_lote
     *  @throws \Exception
     */
    public function creating(Transaccion $nuevo_lote)
    {
        parent::creating($nuevo_lote);
        $nuevo_lote->tipo_transaccion = 35;
        $nuevo_lote->opciones = 2;
        $nuevo_lote->estado = 0;
    }
}
