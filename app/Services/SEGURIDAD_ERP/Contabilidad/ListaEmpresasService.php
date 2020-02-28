<?php


namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Repositories\Repository;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;

class ListaEmpresasService{

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * IncidenciaService constructor.
     * @param Incidencia $model
     */
    public function __construct(Empresa $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate()
    {
        return $this->repository->paginate();
    }

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }
}