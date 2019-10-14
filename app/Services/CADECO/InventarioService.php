<?php


namespace App\Services\CADECO;


use App\Models\CADECO\Inventario;
use App\Repositories\Repository;

class InventarioService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * AlmacenService constructor.
     *
     * @param Inventario $model
     */
    public function __construct(Inventario $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
    public function show($id)
    {
        return $this->repository->show($id);
    }

}