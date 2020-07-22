<?php


namespace App\Services\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use App\Repositories\Repository;

class EfosService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * EfoService constructor.
     * @param EFOS $model
     */
    public function __construct(EFOS $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
