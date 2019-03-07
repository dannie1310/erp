<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 29/01/2019
 * Time: 12:28 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CuentaEmpresa;
use App\Repositories\Repository;

class CuentaEmpresaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CuentaEmpresaService constructor.
     * @param CuentaEmpresa $model
     */
    public function __construct(CuentaEmpresa $model)
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