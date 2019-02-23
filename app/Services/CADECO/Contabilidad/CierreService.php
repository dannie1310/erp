<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 22/02/2019
 * Time: 04:50 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\Cierre;
use App\Repositories\Repository;

class CierreService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CierreService constructor.
     * @param Cierre $model
     */
    public function __construct(Cierre $model)
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