<?php


namespace App\Services\SEGURIDAD_ERP\PadronProveedores;


use App\Repositories\Repository as Repository;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgArea;

class CtgAreaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * EmpresaService constructor.
     * @param Empresa $model
     */
    public function __construct(CtgArea $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

}
