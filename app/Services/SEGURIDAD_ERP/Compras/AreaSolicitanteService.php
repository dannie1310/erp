<?php


namespace App\Services\SEGURIDAD_ERP\Compras;


use App\Models\SEGURIDAD_ERP\Compras\CtgAreaSolicitante;
use App\Repositories\SEGURIDAD_ERP\AreaSolicitante\Repository;

class AreaSolicitanteService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * AreaSolicitanteService constructor.
     * @param CtgAreaSolicitante $model
     */
    public function __construct(CtgAreaSolicitante $model)
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
        return $this->repository->create($data);
    }

    public function asignar($data)
    {
        return $this->repository->asignar($data);
    }
}
