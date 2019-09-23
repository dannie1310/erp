<?php


namespace App\Services\CADECO\Almacenes;


use App\Models\CADECO\Inventarios\Marbete;
use App\Repositories\Repository;

class MarbeteService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * MarbeteService constructor.
     * @param Marbete $model
     */
    public function __construct(Marbete $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

}