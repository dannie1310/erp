<?php


namespace App\Services\CADECO\Almacenes;


use App\Models\CADECO\Inventarios\CtgTipoConteo;
use App\Repositories\Repository;

class CtgTipoConteoService
{
    protected $repository;

    public function __construct(CtgTipoConteo $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

}