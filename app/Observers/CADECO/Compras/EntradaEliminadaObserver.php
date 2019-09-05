<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 06:27 PM
 */

namespace App\Observers\CADECO\Compras;


use App\Models\CADECO\Compras\EntradaEliminada;

class EntradaEliminadaObserver
{
    /**
     * @param EntradaEliminada $entradaEliminada
     */
    public function creating(EntradaEliminada $entradaEliminada)
    {
        $entradaEliminada->fecha_eliminacion = date('Y-m-d H:i:s');
        $entradaEliminada->usuario_elimina = auth()->id();
    }
}