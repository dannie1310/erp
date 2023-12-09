<?php

namespace App\Repositories\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\SolCheque;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class SolicitudChequeRepository extends Repository implements RepositoryInterface
{
    public function __construct(SolCheque $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }
}
