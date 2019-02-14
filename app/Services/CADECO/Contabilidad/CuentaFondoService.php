<?php
/**
 * Created by PhpStorm.
 * User: dbenitezc
 * Date: 11/01/19
 * Time: 04:44 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CuentaFondo;
use App\Repositories\Repository;

class CuentaFondoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CuentaFondoService constructor.
     * @param CuentaFondo $model
     */
    public function __construct(CuentaFondo $model)
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

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }

    public function store(array $data)
    {
        return $this->repository->create($data);
    }
}