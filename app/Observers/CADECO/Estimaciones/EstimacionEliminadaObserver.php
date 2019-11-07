<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 04/11/2019
 * Time: 08:14 PM
 */

namespace App\Observers\CADECO\Estimaciones;


use App\Models\CADECO\Estimaciones\EstimacionEliminada;

class EstimacionEliminadaObserver
{
    /**
     * @param EstimacionEliminada $estimacionEliminada
     */
    public function creating(EstimacionEliminada $estimacionEliminada)
    {
        $estimacionEliminada->fecha_eliminacion = date('Y-m-d H:i:s');
        $estimacionEliminada->usuario_elimina = auth()->id();
    }
}