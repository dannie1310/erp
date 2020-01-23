<?php

namespace App\Observers\CADECO\Finanzas;

use App\Models\CADECO\Finanzas\FacturaEliminada;

class FacturaEliminadaObserver
{
    /**
     * @param FacturaEliminada $eliminada
     */

     public function creating(FacturaEliminada $eliminada)
     {
        $eliminada->validarEstado($eliminada->estado);

        $eliminada->id_usuario_elimino = auth()->id();
        $eliminada->fecha_hora_elimino = date('Y-m-d h:i:s');
     }
}