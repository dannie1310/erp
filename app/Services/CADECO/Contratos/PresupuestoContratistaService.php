<?php


namespace App\Services\CADECO\Contratos;

use App\Models\CADECO\PresupuestoContratista;
use App\Repositories\Repository;

class PresupuestoContratistaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * PresupuestoContratistaService constructor
     * 
     * @param PresupuestoContratista $model
     */
    
     public function __construct(PresupuestoContratista $model)
     {
         $this->repository = new Repository($model);         
     }

     public function paginate($data)
     {
         return $this->repository->paginate($data);
     }

     public function show($id)
     {
         return $this->repository->show($id);
     }
}