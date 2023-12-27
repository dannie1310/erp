<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\PagoAProveedor;
use App\Repositories\CONTROLRECURSOS\PagoAProveedorRepository as Repository;

class PagoAProveedorService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param PagoAProveedor $model
     */
    public function __construct(PagoAProveedor $model)
    {
        $this->repository = new Repository($model);
    }

    public function index(){
        return $this->repository->all();
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
    {dd($data,$id);
        return $this->repository->show($id)->editar($data);
    }

    public function delete($data, $id)
    {
        return $this->repository->show($id)->eliminar();
    }
}
