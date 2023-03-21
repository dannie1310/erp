<?php

namespace App\Repositories\SEGURIDAD_ERP\Concursos;

use App\Models\SEGURIDAD_ERP\Concursos\Concurso;
use App\Models\SEGURIDAD_ERP\Concursos\ConcursoParticipante;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;


class ConcursoParticipanteRepository extends Repository implements RepositoryInterface
{
    public function __construct(ConcursoParticipante $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->registrar($data);
    }
}
