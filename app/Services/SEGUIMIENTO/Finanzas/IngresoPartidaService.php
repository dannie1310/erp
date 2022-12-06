<?php


namespace App\Services\SEGUIMIENTO\Finanzas;


use App\Models\REPSEG\FinDimIngresoPartida;
use App\Repositories\Repository;

class IngresoPartidaService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(FinDimIngresoPartida $model)
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
}
