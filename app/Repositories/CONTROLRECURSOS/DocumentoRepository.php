<?php

namespace App\Repositories\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\Documento;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class DocumentoRepository extends Repository implements RepositoryInterface
{
    public function __construct(Documento $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function registrar($data)
    {
        return $this->model->registrar($data);
    }
}
