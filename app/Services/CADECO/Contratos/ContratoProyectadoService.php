<?php


namespace App\Services\CADECO\Contratos;


use App\Models\CADECO\ContratoProyectado;
use App\Repositories\Repository;

class ContratoProyectadoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ContratoProyectadoService constructor.
     * @param ContratoProyectado $model
     */
    public function __construct(ContratoProyectado $model){
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $solicitudes = $this->repository;

        return $solicitudes->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function index()
    {
        return $this->repository->all();
    }

}