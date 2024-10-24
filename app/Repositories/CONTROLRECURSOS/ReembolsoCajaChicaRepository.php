<?php

namespace App\Repositories\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\ReembolsoCajaChica;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class ReembolsoCajaChicaRepository extends Repository implements RepositoryInterface
{
    public function __construct(ReembolsoCajaChica $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function create(array $datos)
    {
        return $this->model->registrar($datos);
    }

    public function buscar($id)
    {
        return $this->model->withoutGlobalScopes()->find($id);
    }
}
