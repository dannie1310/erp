<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 11:37 AM
 */

namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\DistribucionRecursoRemesa;
use App\Repositories\Repository;

class DistribucionRecursoRemesaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * DistribucionRecursoRemesaService constructor.
     * @param Repository $repository
     */
    public function __construct(DistribucionRecursoRemesa $model)
    {
        $this->repository = new Repository($model);
    }

    public function store(array $data)
    {
        return $this->repository->create($data);
    }

}