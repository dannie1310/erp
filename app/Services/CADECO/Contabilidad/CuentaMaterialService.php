<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/01/2019
 * Time: 12:18 PM
 */

namespace App\Services\CADECO\Contabilidad;



use App\Models\CADECO\Contabilidad\CuentaMaterial;
use App\Repositories\Repository;

class CuentaMaterialService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CuentaMaterialService constructor.
     *
     * @param CuentaMaterial $model
     */
    public function __construct(CuentaMaterial $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function store($data)
    {
        return $this->repository->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }
}