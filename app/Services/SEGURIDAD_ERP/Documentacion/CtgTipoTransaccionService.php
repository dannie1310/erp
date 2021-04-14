<?php


namespace App\Services\SEGURIDAD_ERP\Documentacion;

use App\Repositories\SEGURIDAD_ERP\Documentacion\CtgTipoTransaccionRepository as Repository;
use App\Models\SEGURIDAD_ERP\Documentacion\CtgTipoTransaccion as Model;

class CtgTipoTransaccionService
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
