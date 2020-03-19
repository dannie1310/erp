<?php


namespace App\Services\CADECO\SubcontratosEstimaciones;

use App\Models\CADECO\SubcontratosEstimaciones\PenalizacionLiberacion;
use App\Repositories\Repository;

class PenalizacionLiberacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * PenalizacionLiberacionService constructor
     * @param PenalizacionLiberacion $model
     */
    public function __construct(PenalizacionLiberacion $model)
    {
        $this->repository = new Repository($model);
    }   

    public function index($data)
    {
        return $this->repository->all($data);
    }
}