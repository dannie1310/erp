<?php


namespace App\Services\CADECO\Configuracion;


use App\Models\CADECO\Configuracion\NodoTipo;
use App\Repositories\CADECO\Configuracion\Repository;

class NodoTipoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * MaterialService constructor.
     *
     * @param NodoTipo $model
     */
    public function __construct(NodoTipo $model)
    {
        $this->repository = new Repository($model);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store(array $data)
    {
        return $this->repository->create($data);
    }

    public function delete($data, $id)
    {
        return $this->repository->delete($data, $id);
    }

}
