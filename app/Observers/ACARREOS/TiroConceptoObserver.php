<?php


namespace App\Observers\ACARREOS;


use App\Models\ACARREOS\TiroConcepto;

class TiroConceptoObserver
{
    /**
     * @param TiroConcepto $tiroConcepto
     */
    public function creating(TiroConcepto $tiroConcepto)
    {
        $tiroConcepto->inicio_vigencia = date('Y-m-d H:i:s');
        $tiroConcepto->registro = auth()->id();
        $tiroConcepto->fin_vigencia = NULL;
    }
}
