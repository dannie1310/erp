<?php


namespace App\Observers\CADECO\Subcontratos;


use App\Models\CADECO\Subcontratos\AsignacionSubcontratoEliminado;

class AsignacionSubcontratoEliminadoObserver
{
    /**
     * @param AsignacionSubcontratoEliminado $asignacionSubcontratoEliminado
     */
    public function creating(AsignacionSubcontratoEliminado $asignacionSubcontratoEliminado)
    {
        $asignacionSubcontratoEliminado->fecha_eliminacion = date('Y-m-d H:i:s');
        $asignacionSubcontratoEliminado->usuario_elimina = auth()->id();
    }
}
