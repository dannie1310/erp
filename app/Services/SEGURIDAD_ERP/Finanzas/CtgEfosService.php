<?php


namespace App\Services\SEGURIDAD_ERP\Finanzas;

use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use App\Repositories\SEGURIDAD_ERP\CtgEfos\Repository;

class CtgEfosService
{
    /**
     * @Var Repository
     */
    protected $repository;

    /**
     * CtgEfosService
     * @param CtgEfos $model
     */

    public function __construct(CtgEfos $model)
    {
        $this->repository = new Repository($model);
    }

    public function cargaLayout($file){
        $efos = $this->repository->carga($file);
        return [];
    }

    public function rfcApi($rfc)
    {
        $rest = $this->repository->rfc($rfc);
        return $rest;
    }


    public function paginate($data)
    {
            if(isset($data['rfc']) && isset($data['razon_social'])){
                return $this->repository->where([['rfc','like', '%'.$data['rfc'].'%']])->where([['razon_social','like', '%'.$data['razon_social'].'%']])->paginate();
            }
            if(isset($data['rfc'])){
                return $this->repository->where([['rfc','like', '%'.$data['rfc'].'%']])->paginate();
            }
            if(isset($data['razon_social'])){
                return $this->repository->where([['razon_social','like', '%'.$data['razon_social'].'%']])->paginate();
            }
                return $this->repository->paginate();
    }

    public function obtenerInforme(){
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;
        return $this->repository->getInformeCFD();
    }

}
