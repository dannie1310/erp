<?php

namespace App\Repositories\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;


class ProveedorSATRepository extends Repository implements RepositoryInterface
{
    public function __construct(ProveedorSAT $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function setConnectionSinAcentos()
    {
        $this->model->setConnection("seguridad_Latin1_general_CI_AI");
    }

    public function buscarProveedorAsociar($hints)
    {
        $where_arr = [];
        for($j = 0; $j < count($hints); $j++){
            $where_arr[] = "razon_social COLLATE Latin1_general_CI_AI like '%".$hints[$j]."%'";
            $where_arr[] = "rfc COLLATE Latin1_general_CI_AI like '".$hints[$j]."'";
        }
        if(count($where_arr)>0){
            $where = implode(" OR ", $where_arr);
        }
        $proveedorSAT = ProveedorSAT::whereRaw($where)->OrderBy("razon_social")->get();
        return $proveedorSAT;
    }

}
