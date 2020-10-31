<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 05:03 PM
 */

namespace App\Observers\CADECO\Subcontratos;


use App\Facades\Context;
use App\Models\CADECO\Subcontratos\AsignacionContratista;

class AsignacionContratistaObserver
{
    /**
     * @param AsignacionContratista $asignacion_contratista
     */
    public function creating(AsignacionContratista $asignacion_contratista)
    {
        $asignacion_contratista->fecha_hora_registro = date('Y-m-d H:i:s');
        $asignacion_contratista->fecha_hora_autorizacion = date('Y-m-d H:i:s');
        $asignacion_contratista->registro = auth()->id();
        $asignacion_contratista->autorizo = auth()->id();
    }

    public function deleting(AsignacionContratista $asignacion_contratista)
    {
        $asignacion_contratista->validarParaEliminar();
        if($asignacion_contratista->asignacionEliminada == null)
        {
            abort(400, "Error al eliminar, respaldo incorrecto.");
        }
    }
}
