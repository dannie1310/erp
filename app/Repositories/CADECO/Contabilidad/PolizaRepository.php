<?php

namespace App\Repositories\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\Poliza;
use App\Traits\RepositoryTrait;

class PolizaRepository
{
    use RepositoryTrait;

    /**
     * @var Poliza
     */
    private $model;

    /**
     * PolizaRepository constructor.
     * @param Poliza $model
     */
    public function __construct(Poliza $model)
    {
        $this->model = $model;
    }
}