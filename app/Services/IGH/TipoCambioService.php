<?php


namespace App\Services\IGH;


use App\Models\IGH\TipoCambio;
use App\Repositories\Repository;

class TipoCambioService
{

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TipoCambioService constructor.
     * @param TipoCambio $model
     */
    public function __construct(TipoCambio $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
