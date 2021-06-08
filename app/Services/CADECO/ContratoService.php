<?php


namespace App\Services\CADECO;


use App\Models\CADECO\Contrato;
use App\Repositories\Repository;

class ContratoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ContratoService constructor.
     * @param Contrato $model
     */
    public function __construct(Contrato $model)
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
