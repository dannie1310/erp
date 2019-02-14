<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 16/01/2019
 * Time: 01:18 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CuentaGeneral;
use App\Repositories\Repository;

class CuentaGeneralService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CuentaGeneralService constructor.
     * @param CuentaGeneral $model
     */
    public function __construct(CuentaGeneral $model)
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