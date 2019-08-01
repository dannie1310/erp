<?php

namespace App\Observers\CADECO\Finanzas;

use App\Models\CADECO\Finanzas\DistribucionRecursoRemesa;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaLog;

class DistribucionRecursoRemesaObserver
{
    /**
     * @param DistribucionRecursoRemesa $distribucion
     */
    public function created(DistribucionRecursoRemesa $distribucion)
    {
        DistribucionRecursoRemesaLog::query()->create([
            'id_recurso_remesa' => $distribucion->id,
            'id_estado' => $distribucion->estado,
            'accion' => 'Genera Distribución'
        ]);
    }

    public function updated(DistribucionRecursoRemesa $distribucion)
    {
        DistribucionRecursoRemesaLog::query()->create([
            'id_recurso_remesa' => $distribucion->id,
            'id_estado' => $distribucion->estado,
            'accion' => 'Actualiza Distribución a ' . $distribucion->estatus->descripcion
        ]);
    }
//    public function retrieved (DistribucionRecursoRemesa $distribucion)
//    {
//        DistribucionRecursoRemesaLog::query()->create(['id_recurso_remesa' => $distribucion->id, 'id_estado' => $distribucion->estado]);
//    }
}
