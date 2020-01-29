<?php

/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 16/23/20
 * Time: 05:33 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Suministrados;
use App\Repositories\CADECO\Suministrados\Repository;

class SuministradoService{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SucursalService constructor
     *
     * @param Suministrados $model
     */

    public function __construct(Suministrados $model)
    {
        $this->repository = new Repository($model);
    }

    public function store(array $data)
    {
        return $this->repository->create($data);
    }

    public function delete($data, $id)
    {
        return $this->repository->delete($data, $id);
    }

}