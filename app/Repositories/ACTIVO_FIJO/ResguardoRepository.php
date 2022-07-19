<?php


namespace App\Repositories\ACTIVO_FIJO;

use App\Models\ACTIVO_FIJO\Resguardo;
use App\Repositories\RepositoryInterface;

class ResguardoRepository extends \App\Repositories\Repository implements RepositoryInterface
{
    public function __construct(Resguardo $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getListaResguardos($data){
        $resg = $this->model->select('GrupoEquipo')->distinct()->orderBy('GrupoEquipo', 'ASC')->get();
        $data = [];
        foreach($resg as $resguardo){
            $data[] = ["idGrupo"=>$resguardo->GrupoEquipo, "Descripcion" => $resguardo->tipoGrupoActivo];
        }
        return $data;
    }
}