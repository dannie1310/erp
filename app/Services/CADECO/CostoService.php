<?php

namespace App\Services\CADECO;


use App\Models\CADECO\Costo;
use App\Repositories\Repository;

class CostoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CostoService constructor.
     * @param Costo $model
     */
    public function __construct(Costo $model)
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
}