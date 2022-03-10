<?php


namespace App\Observers\CADECO;


use App\Models\CADECO\ItemSolicitudAutorizacionAvance;

class SolicitudAutorizacionAvancePartidaObserver
{
    /**
     * @param ItemSolicitudAutorizacionAvance $partida
     */
    public function creating(ItemSolicitudAutorizacionAvance $partida)
    {
        $partida->validarCantidadesPartidas();
    }

    public function updating(ItemSolicitudAutorizacionAvance $partida)
    {
        $partida->validarCantidadesPartidas();
    }

    public function deleting(ItemSolicitudAutorizacionAvance $partida)
    {
        if($partida->itemEliminado == null)
        {
            abort(400, "Error al eliminar, respaldo incorrecto.");
        }
    }
}
