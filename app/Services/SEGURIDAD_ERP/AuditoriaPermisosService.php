<?php


namespace App\Services\SEGURIDAD_ERP;

use App\Models\SEGURIDAD_ERP\Proyecto;
use App\Repositories\Repository;


class AuditoriaPermisosService
{
    protected $repository;

    public function __construct(Proyecto $model){
        $this->repository = new Repository($model);
    }

    public function index()
    {
        dd(count($this->repository->all()));
        return $this->repository->all();
    }
    public function paginate()
    {
        return $this->repository->paginate();
    }

}