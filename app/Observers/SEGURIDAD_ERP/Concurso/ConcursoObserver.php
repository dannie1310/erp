<?php

namespace App\Observers\SEGURIDAD_ERP\Concurso;

use App\Models\SEGURIDAD_ERP\Concursos\Concurso;

class ConcursoObserver
{
    /**
     * @param Concurso $concurso
     * @return void
     */
    public function creating(Concurso $concurso)
    {
        $concurso->id_usuario_inicio_apertura = auth()->id();
        $concurso->fecha_hora_inicio_apertura = date('Y-m-d h:i:s');
    }

    public function updating(Concurso $concurso)
    {
        if($concurso->estatus != $concurso->getOriginal('estatus') && $concurso->estatus == 2)
        {
            $concurso->id_usuario_finalizo_apertura = auth()->id();
            $concurso->fecha_hora_fin_apertura = date('Y-m-d h:i:s');
        }
    }
}
