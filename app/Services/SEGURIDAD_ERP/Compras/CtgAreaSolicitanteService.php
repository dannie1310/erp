<?php


namespace App\Services\SEGURIDAD_ERP\Compras;


use App\Models\SEGURIDAD_ERP\Compras\CtgAreaSolicitante;
use App\Repositories\Repository;

class CtgAreaSolicitanteService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CtgAreaSolicitanteService constructor
     * @param CtgAreaSolicitante $model
     */
    public function __construct(CtgAreaSolicitante $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
