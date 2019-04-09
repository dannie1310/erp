<?php

namespace App\Services\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\TransaccionInterfaz;
use App\Repositories\Repository;

class TransaccionInterfazService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TransaccionInterfazService constructor.
     * @param TransaccionInterfaz $model
     */
    public function __construct(TransaccionInterfaz $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}