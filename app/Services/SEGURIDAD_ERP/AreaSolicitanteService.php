<?php


namespace App\Services\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\TipoAreaSolicitante;
use App\Repositories\SEGURIDAD_ERP\AreaSolicitante\Repository;

class AreaSolicitanteService
{
    protected $repository;

    public function __construct(TipoAreaSolicitante $model)
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
