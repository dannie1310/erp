<?php


namespace App\Services\SEGURIDAD_ERP\Compras;


use App\Models\SEGURIDAD_ERP\Compras\CtgAreaCompradora;
use App\Repositories\SEGURIDAD_ERP\AreaCompradora\Repository;

class AreaCompradoraService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * AreaCompradoraService constructor.
     * @param CtgAreaCompradora $model
     */
    public function __construct(CtgAreaCompradora $model)
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
