<?php


namespace App\Services\SEGURIDAD_ERP;

use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Permiso;
use App\Repositories\Repository;

class PermisoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * PermisoService constructor.
     * @param Permiso $model
     */
    public function __construct(Permiso $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function porUsuario($id)
    {
        return Usuario::query()->find($id)->permisos();
    }
}