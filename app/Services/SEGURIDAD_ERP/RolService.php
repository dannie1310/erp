<?php


namespace App\Services\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\Rol;
use App\Repositories\Repository;

class RolService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * RolService constructor.
     * @param Rol $model
     */
    public function __construct(Rol $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}