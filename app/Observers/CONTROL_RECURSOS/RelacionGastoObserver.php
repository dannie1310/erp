<?php

namespace App\Observers\CONTROL_RECURSOS;

use App\Models\CONTROL_RECURSOS\RelacionGasto;
use App\Models\CONTROL_RECURSOS\Serie;

class RelacionGastoObserver
{
    /**
     * @param RelacionGasto $relacion
     */
    public function creating(RelacionGasto $relacion)
    {
        $serie = Serie::where('idseries', $relacion->idserie)->pluck('Descripcion')->first();
        $folio =  $relacion->getNumeroFolio();
        $relacion->numero_folio = $folio;
        $relacion->folio = $serie . "-" . $folio;
        $relacion->modifico_estado = auth()->id();
        $relacion->idestado = 1;
        $relacion->registro = auth()->id();
        $relacion->registro_portal = 1;
    }

    public function created(RelacionGasto $relacion)
    {
        /**
         * Se realiza la función para agregar los estados a tablas adicionales, pero ya se realiza por medio de SP
         */
        //$relacion->agregarEstados();
    }

    public function updating(RelacionGasto $relacionGasto)
    {
        if($relacionGasto->getOriginal('idserie') != $relacionGasto->idserie)
        {
            $serie = Serie::where('idseries', $relacionGasto->idserie)->pluck('Descripcion')->first();
            $relacionGasto->folio = $serie . "-" . $relacionGasto->numero_folio;
        }

        if($relacionGasto->getOriginal('idestado') != $relacionGasto->idestado)
        {
            $relacionGasto->modifico_estado = auth()->id();
        }
    }

    public function deleting(RelacionGasto $relacion)
    {
        $relacion->respaldar();
        if($relacion->relacionEliminada == null)
        {
            abort(400, "Error al eliminar, respaldo incorrecto.");
        }
    }
}
