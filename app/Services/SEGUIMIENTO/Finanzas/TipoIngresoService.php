<?php


namespace App\Services\SEGUIMIENTO\Finanzas;


use App\Models\REPSEG\FinDimTipoIngreso;
use App\Repositories\Repository;

class TipoIngresoService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(FinDimTipoIngreso $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function store($data)
    {
        try {
            return $this->repository->create($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
