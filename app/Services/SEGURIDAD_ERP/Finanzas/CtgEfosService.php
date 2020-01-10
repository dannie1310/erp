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



    public function paginate($data)
    {
        return $this->repository->paginate($data);

    }

}
