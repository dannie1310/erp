<?php

namespace App\Repositories\SEGURIDAD_ERP\Concursos;

use App\Models\SEGURIDAD_ERP\Concursos\Concurso;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;


class ConcursoRepository extends Repository implements RepositoryInterface
{
    public function __construct(Concurso $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->registrar($data);
    }

    public function ultimo()
    {
        return $this->model->ultimo();
    }
}
