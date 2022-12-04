<?php


namespace App\Services\SEGUIMIENTO\Finanzas;


use App\Models\REPSEG\GrlMoneda;
use App\Repositories\Repository;

class MonedaService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(GrlMoneda $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
