<?php


namespace App\Services\CADECO\Configuracion;


use App\Models\CADECO\Configuracion\NodoTipo;
use App\Repositories\Repository;

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

}
