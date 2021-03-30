<?php


namespace App\Services\SEGURIDAD_ERP\Documentacion;

use App\Repositories\SEGURIDAD_ERP\Documentacion\CtgTipoArchivoRepository as Repository;
use App\Models\SEGURIDAD_ERP\Documentacion\CtgTipoArchivo as Model;

class CtgTipoArchivoService
{
    protected $repository;

    public function __construct(Model $model)
    {
        $this->repository = new Repository($model);
    }

    public function index()
    {
        return $this->repository->all();
    }
}
