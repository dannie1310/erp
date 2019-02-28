<?php

namespace App\Services\CADECO\Contabilidad;



use App\Models\CADECO\Contabilidad\CuentaCosto;
use App\Repositories\Repository;

class CuentaCostoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CuentaCostoService constructor.
     *
     * @param CuentaCosto $model
     */
    public function __construct(CuentaCosto $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function store($data)
    {
        return $this->repository->create($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }
}