<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\ReembolsoPagoAProveedor;
use App\Repositories\CONTROLRECURSOS\ReembolsoPagoAProveedorRepository as Repository;

class ReembolsoPagoAProveedorService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param ReembolsoPagoAProveedor $model
     */
    public function __construct(ReembolsoPagoAProveedor $model)
    {
        $this->repository = new Repository($model);
    }

    public function store(array $data)
    {
        try {
            return $this->repository->create($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function update(array $data, $id)
    {
        return $this->repository->show($id)->editar($data);
    }

    public function delete($data, $id)
    {
        return $this->repository->show($id)->eliminar();
    }
}
