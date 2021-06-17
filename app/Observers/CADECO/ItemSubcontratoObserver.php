<?php


namespace App\Observers\CADECO;


use App\Models\CADECO\ItemSubcontrato;

class ItemSubcontratoObserver
{
    /**
     * @param ItemSubcontrato $itemSubcontrato
     */
    public function deleting(ItemSubcontrato $itemSubcontrato)
    {
        if($itemSubcontrato->partidaSubcontratoEliminada == null)
        {
            abort(400, "Error al eliminar, respaldo incorrecto.");
        }
    }
}
