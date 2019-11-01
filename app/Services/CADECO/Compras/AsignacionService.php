<?php


namespace App\Services\CADECO\Compras;


use App\Models\CADECO\Compras\Asignacion;
use App\PDF\AsignacionFormato;
use App\Repositories\Repository;

class AsignacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(Asignacion $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function asignacion($id)
    {
        $pdf = new AsignacionFormato($id);
        return $pdf;
    }
}
