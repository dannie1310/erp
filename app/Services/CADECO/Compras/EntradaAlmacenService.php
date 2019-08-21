<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 12:57 PM
 */

namespace App\Services\CADECO\Compras;


use App\Models\CADECO\EntradaMaterial;
use App\Repositories\Repository;

class EntradaAlmacenService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * EntradaAlmacenService constructor.
     * @param EntradaMaterial $model
     */
    public function __construct(EntradaMaterial $model)
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

    public function delete($data, $id)
    {
        dd($data, $id);
    }
}