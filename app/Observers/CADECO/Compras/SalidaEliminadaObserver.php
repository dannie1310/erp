<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 06:32 PM
 */

namespace App\Observers\CADECO\Compras;


use App\Models\CADECO\Compras\SalidaEliminada;

class SalidaEliminadaObserver
{
    /**
     * @param SalidaEliminada $salidaEliminada
     */
    public function creating(SalidaEliminada $salidaEliminada)
    {
        $salidaEliminada->fecha_eliminacion = date('Y-m-d H:i:s');
        $salidaEliminada->usuario_elimina = auth()->id();
    }
}