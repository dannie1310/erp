<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\CcDocto;
use App\Repositories\Repository;

class CcDoctoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param CcDocto $model
     */
    public function __construct(CcDocto $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function update(array $data, $id)
    {
        return $this->repository->show($id)->editar($data);
    }
}
