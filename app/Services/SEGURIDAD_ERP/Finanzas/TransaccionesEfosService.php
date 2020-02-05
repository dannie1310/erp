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

        if(isset($data['numero_folio'])){
            return $this->repository->where([['numero_folio','like', '%'.$data['numero_folio'].'%']])->paginate();
        }
        return $this->repository->paginate($data);
    }
}