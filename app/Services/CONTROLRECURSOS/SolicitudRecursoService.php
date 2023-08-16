<?php

namespace App\Services\CONTROLRECURSOS;

use App\LAYOUT\LayoutBancario;
use App\Models\CONTROL_RECURSOS\SolrecSemanaAnio;
use App\Models\CONTROL_RECURSOS\SolRecurso;
use App\Repositories\Repository;

class SolicitudRecursoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param SolRecurso $model
     */
    public function __construct(SolRecurso $model)
    {
        $this->repository = new Repository($model);
    }

    public function layout($id){
        $time = SolrecSemanaAnio::where('idsemana_anio', $id)->first();
        $layout = new LayoutBancario($time->semana, $time->anio);
        return $layout->create();
    }

}
