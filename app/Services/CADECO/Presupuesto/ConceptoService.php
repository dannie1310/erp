<?php

namespace App\Services\CADECO\Presupuesto;


use App\Models\CADECO\Concepto;
use App\Repositories\CADECO\Presupuesto\Concepto\Repository;

class ConceptoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ConceptoService constructor.
     * @param Concepto $model
     */
    public function __construct(Concepto $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function list($id_padre)
    {
        return $this->repository->list($id_padre);
    }
}
