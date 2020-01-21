<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/11/2019
 * Time: 08:02 PM
 */

namespace App\Observers\CADECO\Compras;


use App\Models\CADECO\Compras\RequisicionPartidaComplemento;

class RequisicionPartidaComplementoObserver
{
    /**
     * @param RequisicionPartidaComplemento $requisicionComplemento
     */

    public function creating(RequisicionPartidaComplemento $requisicionComplemento)
    {
        $requisicionComplemento->usuario_registro = auth()->id();
        $requisicionComplemento->timestamp_registro = date('Y-m-d H:i:s');
    }
}