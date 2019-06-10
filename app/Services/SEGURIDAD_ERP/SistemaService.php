<?php


namespace App\Services\SEGURIDAD_ERP;


use App\Models\SEGURIDAD_ERP\Sistema;
use App\Repositories\Repository;

class SistemaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SistemaService constructor.
     * @param Sistema $model
     */
    public function __construct(Sistema $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}