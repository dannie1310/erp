<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 09/06/2020
 * Time: 02:27 PM
 */

namespace App\Observers\CADECO\Compras;

use App\Models\CADECO\Compras\OrdenCompraComplemento;

class OrdenCompraComplementoObserver
{
    /**
     * @param OrdenCompraComplemento $asignacion_proveedor
     */
    public function creating(OrdenCompraComplemento $complemento)
    {
        $complemento->timestamp_registro = date('Y-m-d H:i:s');
        $complemento->registro = auth()->id();
    }

}