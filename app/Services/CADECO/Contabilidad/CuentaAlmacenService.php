<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 11:13 AM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CuentaAlmacen;
use App\Repositories\Repository;

class CuentaAlmacenService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CuentaAlmacenService constructor.
     * @param CuentaAlmacen $model
     */
    public function __construct(CuentaAlmacen $model)
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
}