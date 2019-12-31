<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/11/2019
 * Time: 06:40 PM
 */

namespace App\Observers\CADECO\Compras;


use App\Models\CADECO\Compras\RequisicionComplemento;

class RequisicionComplementoObserver
{
    /**
     * @param RequisicionComplemento $requisicionComplemento
     */

    public function creating(RequisicionComplemento $requisicionComplemento)
    {
        $requisicionComplemento->folio_compuesto = $requisicionComplemento->generaFolioCompuesto();
        $requisicionComplemento->registro = auth()->id();
    }
}