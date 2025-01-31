<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\TasaIva;
use App\Repositories\Repository;

class TasaIvaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param TasaIva $model
     */
    public function __construct(TasaIva $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
