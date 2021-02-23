<?php


namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\ComprobanteFondo;
use App\Repositories\Repository;

class ComprobanteFondoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ComprobanteFondoService constructor.
     * @param ComprobanteFondo $model
     */

    public function  __construct(ComprobanteFondo $model){
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $comprobantes = $this->repository;

        return $comprobantes->paginate($data);
    }
}
