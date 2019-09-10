<?php

namespace App\Observers\CADECO\Finanzas;

use App\Facades\Context;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesa;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaLog;

class DistribucionRecursoRemesaObserver
{
    /**
     * @param DistribucionRecursoRemesa $distribucion
     */

    public function creating(DistribucionRecursoRemesa $distribucion)
    {
        $count = DistribucionRecursoRemesa::query()->count('id');
        $distribucion->id_obra = Context::getIdObra();
        $distribucion->folio = $count +1;
        $distribucion->usuario_registro = auth()->id();
        $distribucion->fecha_hora_registro = date('Y-m-d H:i:s');
        $distribucion->estado = 0;
    }

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
