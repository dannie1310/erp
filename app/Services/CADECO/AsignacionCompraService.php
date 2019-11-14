<?php


namespace App\Services\CADECO;


use App\Models\CADECO\AsignacionCompra;
use App\Repositories\Repository;

class AsignacionCompraService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SolicitudCompraService constructor.
     * @param AsignacionCompra $model
     */
    public function __construct(AsignacionCompra $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

}
