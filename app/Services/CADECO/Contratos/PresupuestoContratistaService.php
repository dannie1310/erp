<?php


namespace App\Services\CADECO\Contratos;

use App\Models\CADECO\PresupuestoContratista;
use App\Repositories\CADECO\PresupuestoContratista\Repository;

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

     public function update(array $data, $id)
     {
         return $this->repository->show($id)->actualizar($data);
     }

     public function delete($data, $id)
    {
        return $this->show($id)->eliminarPresupuesto($data['data']);
    }

    public function store($data)
    {
        return $this->repository->create($data);
    }
}