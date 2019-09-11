<?php


namespace App\Services\CADECO\Almacenes;

use App\Models\CADECO\Inventarios\InventarioFisico;
use App\Repositories\Repository;

class InventarioFisicoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * InventarioFisicoService constructor.
     * @param InventarioFisico $model
     */
    public function __construct(InventarioFisico $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function store($data)
    {
//        dd($data);
        $registro = $this->repository->create(['folio' => 66]);
        return $registro;
    }

}