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

    public function delete($data, $id){
        return $this->repository->update(['estado' => 0],$id);
    }

    public function store($data)
    {
        return $this->repository->create($data);
    }
}
