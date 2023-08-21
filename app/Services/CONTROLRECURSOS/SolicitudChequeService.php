<?php

namespace App\Services\CONTROLRECURSOS;

use App\LAYOUT\LayoutBancario;
use App\Models\CONTROL_RECURSOS\SolCheque;
use App\Models\CONTROL_RECURSOS\SolrecSemanaAnio;
use App\Repositories\Repository;

class SolicitudChequeService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param SolCheque $model
     */
    public function __construct(SolCheque $model)
    {
        $this->repository = new Repository($model);
    }

    public function layout($data)
    {
        $layout = new LayoutBancario($data);
        return $layout->create();
    }

    public function index(){
        return $this->repository->all();
    }
}
