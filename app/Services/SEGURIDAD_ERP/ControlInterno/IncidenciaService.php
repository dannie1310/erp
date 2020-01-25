<?php


namespace App\Services\SEGURIDAD_ERP\ControlInterno;


use App\Models\SEGURIDAD_ERP\ControlInterno\Incidencia;
use App\Repositories\Repository;

class IncidenciaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * IncidenciaService constructor.
     * @param Incidencia $model
     */
    public function __construct(Incidencia $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate()
    {
        return $this->repository->paginate();
    }
}
