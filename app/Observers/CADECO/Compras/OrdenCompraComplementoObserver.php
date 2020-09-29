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

    public function deleted(OrdenCompraComplemento $complemento)
    {
        /**
         * Cambiar estado de la asignación a: Registrada
         */
        $otras = OrdenCompraComplemento::where("id_asignacion_proveedor","=",$complemento->id_asignacion_proveedor)
            ->where("id_transaccion","!=",$complemento->id_transaccion)->get();
        if(count($otras)==0)
        {
            $complemento->asignacion->estado = 1;
            $complemento->asignacion->save();
        }
    }

}
