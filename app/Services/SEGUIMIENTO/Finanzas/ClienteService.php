<?php


namespace App\Services\SEGUIMIENTO\Finanzas;


use App\Models\REPSEG\FinDimIngresoCliente;
use App\Repositories\Repository;

class ClienteService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(FinDimIngresoCliente $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
