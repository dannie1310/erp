<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\ReembolsoGastoSol;
use App\Repositories\CONTROLRECURSOS\ReembolsoGastoSolRepository as Repository;

class ReembolsoGastoSolService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param ReembolsoGastoSol $model
     */
    public function __construct(ReembolsoGastoSol $model)
    {
        $this->repository = new Repository($model);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function update(array $data, $id)
    {
        return $this->repository->show($id)->editar($data);
    }

    public function delete($data, $id)
    {
        return $this->repository->show($id)->eliminar();
    }
}
