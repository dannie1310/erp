<?php


namespace App\Services\SEGURIDAD_ERP\Finanzas;


use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDI;
use App\Repositories\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDIRepository as Repository;

class SolicitudRecepcionCFDIService
{
    protected $repository;

    public function __construct(SolicitudRecepcionCFDI $model)
    {
        $this->repository = new Repository($model);
    }

    public function index()
    {
        return $this->repository->all();
    }

    public function paginate()
    {
        return $this->repository->paginate();
    }

}
