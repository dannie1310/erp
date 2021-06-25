<?php


namespace App\Observers\CADECO\Subcontratos;


use App\Models\CADECO\Subcontratos\AsignacionSubcontrato;

class AsignacionSubcontratoObserver
{
    /**
     * @param AsignacionSubcontrato $subcontrato
     */
    public function deleting(AsignacionSubcontrato $subcontrato)
    {
        if($subcontrato->asignacionSubcontratoEliminado == null)
        {
            abort(400, "Error al eliminar, respaldo incorrecto.");
        }
    }

    public function deleted(AsignacionSubcontrato $subcontrato)
    {
        $subcontrato->editarEstadoAsignacion();
    }
}
