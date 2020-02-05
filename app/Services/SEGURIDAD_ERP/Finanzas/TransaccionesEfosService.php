<?php


namespace App\Services\SEGURIDAD_ERP\Finanzas;


use App\Models\SEGURIDAD_ERP\Finanzas\TransaccionesEfos;
use App\Repositories\Repository;

class TransaccionesEfosService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TransaccionesEfosService constructor
     * @param TransaccionesEfos $model
     */

     public function __construct(TransaccionesEfos $model)
     {
         $this->repository = new Repository($model);
     }

     public function paginate($data)
    {
        
        // dd('Paginate transaccion efos', $data);
        return $this->repository->paginate($data);
    }
}