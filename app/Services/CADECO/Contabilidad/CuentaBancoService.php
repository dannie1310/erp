<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/02/2019
 * Time: 04:20 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CuentaBanco;
use App\Repositories\Repository;

class CuentaBancoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CuentaBancoService constructor.
     * @param CuentaBanco $model
     */
    public function __construct(CuentaBanco $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store($data)
    {
        return $this->repository->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }

    public function delete($data, $id)
    {
        return $this->repository->delete($data, $id);
    }
}