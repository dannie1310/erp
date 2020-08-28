<?php


namespace App\Services\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgEspecialidad as Model;
use App\Repositories\SEGURIDAD_ERP\PadronProveedores\EspecialidadRepository as Repository;

class EspecialidadService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * GiroService constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
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

    public function store($data)
    {
        return $this->repository->store($data);
    }
}
