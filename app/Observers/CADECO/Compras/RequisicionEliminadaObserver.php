<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 27/11/2019
 * Time: 07:43 PM
 */

namespace App\Observers\CADECO\Compras;


use App\Models\CADECO\Compras\RequisicionEliminada;

class RequisicionEliminadaObserver
{
    /**
     * @param RequisicionEliminada $requisicionEliminada
     */
    public function creating(RequisicionEliminada $requisicionEliminada)
    {
        $requisicionEliminada->fecha_eliminacion = date('Y-m-d H:i:s');
        $requisicionEliminada->usuario_elimina = auth()->id();
    }
}