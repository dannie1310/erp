<?php


namespace App\Services\SEGUIMIENTO\Finanzas;


use App\Models\REPSEG\FinDimIngresoEmpresa;
use App\Repositories\Repository;

class EmpresaService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(FinDimIngresoEmpresa $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
