<?php


namespace App\Services\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Fiscal\CtgTipoFecha;
use App\Repositories\Repository;

class TipoFechaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TipoFechaService constructor.
     * @param CtgTipoFecha $model
     */
    public function __construct(CtgTipoFecha $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}
