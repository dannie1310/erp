<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\TipoDoctoComp;
use App\Repositories\Repository;

class TipoDocCompService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param TipoDoctoComp $model
     */
    public function __construct(TipoDoctoComp $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
