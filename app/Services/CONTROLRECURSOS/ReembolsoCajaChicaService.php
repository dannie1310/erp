<?php

namespace App\Services\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\ReembolsoCajaChica;
use App\Repositories\CONTROLRECURSOS\ReembolsoCajaChicaRepository as Repository;

class ReembolsoCajaChicaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param ReembolsoCajaChica $model
     */
    public function __construct(ReembolsoCajaChica $model)
    {
        $this->repository = new Repository($model);
    }

    public function store(array $data)
    {
        try {
            return $this->repository->create($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function show($id)
    {
        return $this->repository->buscar($id);
    }

    public function delete($data, $id)
    {
        return $this->repository->buscar($id)->eliminar();
    }
}
