<?php

namespace App\Services\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CuentaConcepto;
use App\Repositories\Repository;

class CuentaConceptoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CuentaConceptoService constructor.
     * @param CuentaConcepto $model
     */
    public function __construct(CuentaConcepto $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
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