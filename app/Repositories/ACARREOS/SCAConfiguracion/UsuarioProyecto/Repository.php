<?php


namespace App\Repositories\ACARREOS\SCAConfiguracion\UsuarioProyecto;

use App\Models\ACARREOS\SCA_CONFIGURACION\UsuarioProyecto;
use App\Repositories\RepositoryInterface;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    public function __construct(UsuarioProyecto $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getChecadores($id_proyecto, $id_checador){
        return $this->model->esChecador()->sinTelefono($id_checador)->where('id_proyecto', '=', $id_proyecto)->get();
    }
}