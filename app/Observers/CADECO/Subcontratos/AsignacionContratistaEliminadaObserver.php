<?php


namespace App\Observers\CADECO\Subcontratos;


use App\Models\CADECO\Subcontratos\AsignacionContratistaEliminada;

class AsignacionContratistaEliminadaObserver
{
    /**
     * @param AsignacionContratistaEliminada $asignacionContratistaEliminada
     */
    public function creating(AsignacionContratistaEliminada $asignacionContratistaEliminada)
    {
        $asignacionContratistaEliminada->fecha_eliminacion = date('Y-m-d H:i:s');
        $asignacionContratistaEliminada->usuario_elimina = auth()->id();
    }
}
