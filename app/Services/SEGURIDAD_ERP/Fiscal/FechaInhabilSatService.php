<?php

namespace App\Services\SEGURIDAD_ERP\Fiscal;

use App\Repositories\Repository;
use App\Models\SEGURIDAD_ERP\Fiscal\FechaInhabilSat;

class FechaInhabilSatService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * FechaInhabilSatService constructor.
     * @param FechaInhabilSat $model
     */
    public function __construct(FechaInhabilSat $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function eliminar($id){
        $item = $this->repository->show($id);
        $item->eliminar();
        return $item;
    }

    public function store($data)
    {
        return $this->repository->create($data);
    }
}
