<?php


namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Repositories\Repository;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;

class ProveedorSATService{

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ProveedorSATService constructor.
     * @param ProveedorSAT $model
     */
    public function __construct(ProveedorSAT $model)
    {
        $this->repository = new Repository($model);
    }

    public function buscarProveedorAsociar($data){
        $hints = explode(' ', $data['nombre']);
        foreach($hints as $i => $hint){
            if(strlen(trim($hint)) < 3){
                unset($hints[$i]);
            }
        }
        $hints = array_values($hints);
        for($j = 0; $j < count($hints); $j++){
            $this->repository->whereOr([['razon_social','like',"%$hints[$j]%"]]);
        }
        return  $this->repository->all();
    }

}
