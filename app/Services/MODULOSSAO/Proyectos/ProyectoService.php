<?php

namespace App\Services\MODULOSSAO\Proyectos;

use App\Models\MODULOSSAO\Proyectos\Proyecto;
use App\Repositories\MODULOSSAO\Proyectos\ProyectoRepository as Repository;

class ProyectoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ProyectoService constructor.
     * @param Proyecto $model
     */
    public function __construct(Proyecto $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id){
        return $this->repository->show($id);
    }

    public function paginate($id){
        return $this->repository->paginate();
    }

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }
}
