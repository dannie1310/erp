<?php


namespace App\Observers\CADECO;


use App\Models\CADECO\ItemComprobanteFondo;

class ComprobanteFondoPartidaObserver
{
    /**
     * @param ItemComprobanteFondo $partida
     */
    public function deleting(ItemComprobanteFondo $partida)
    {
        $partida->respaldar();
        if($partida->partidaRespaldo == null)
        {
            abort(400, "Error al eliminar, respaldo incorrecto.");
        }
    }
}
