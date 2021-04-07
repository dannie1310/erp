<?php


namespace App\Services\SEGURIDAD_ERP;

use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Obra;
use App\Models\SEGURIDAD_ERP\Rol;
use App\Repositories\Repository;


class ObraService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * RolService constructor.
     * @param Rol $model
     */
    public function __construct(Obra $model)
    {
        $this->repository = new Repository($model);
    }

    public function index()
    {
        return $this->repository->all();
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

}
