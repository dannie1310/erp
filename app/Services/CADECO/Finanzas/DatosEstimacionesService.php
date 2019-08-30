<?php


namespace App\Services\CADECO\Finanzas;
use App\Models\CADECO\Finanzas\DatosEstimaciones;
use App\Repositories\Repository;


class DatosEstimacionesService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * DatosEstimacionesService constructor.
     * @param DatosEstimaciones $model
     */
    public function __construct(DatosEstimaciones $model)
    {
        $this->repository = new Repository($model);
    }

    public function store($data){
        return $this->repository->create($data['data']);
    }

}