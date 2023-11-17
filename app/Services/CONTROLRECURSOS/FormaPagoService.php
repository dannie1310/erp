<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\FormaPago;
use App\Repositories\Repository;

class FormaPagoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param FormaPago $model
     */
    public function __construct(FormaPago $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
