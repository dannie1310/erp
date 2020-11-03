<?php


namespace App\Observers\CADECO\Subcontratos;


use App\Models\CADECO\Subcontratos\AsignacionContratistaPartida;

class AsignacionContratistaPartidaObserver
{
    /**
     * @param AsignacionContratistaPartida $partida
     */
    public function deleting(AsignacionContratistaPartida $partida)
    {
        if($partida->partidaEliminada == null)
        {
            abort(400, "Error al eliminar, respaldo de partida incorrecto.");
        }
    }
}
