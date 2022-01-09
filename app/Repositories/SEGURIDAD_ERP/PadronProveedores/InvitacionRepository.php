<?php

namespace App\Repositories\SEGURIDAD_ERP\PadronProveedores;

use App\Models\CADECO\Transaccion;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgTipoArchivoInvitacion;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion as Model;

class InvitacionRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function store($data)
    {
        return $this->model->registrar($data);
    }

    public function getPorCotizar()
    {
       return $this->model->getSolicitudes();
    }

    public function getTiposArchivo($data)
    {
        if(key_exists("id",$data))
        {
            $invitacion = Invitacion::find($data["id"]);
            $areas = [];
            $tipos = [];
            if($invitacion->id_area_compradora > 0)
            {
                $areas =[1,3];

            } else
            if($invitacion->id_area_contatante > 0)
            {
                $areas =[2,3];

            }
            if(key_exists("global", $data) && $data["global"])
            {
                $tipos = [2,3];
            }else{
                $tipos = [1,3];
            }

            $tipos = CtgTipoArchivoInvitacion::where("estatus","=",1)
                ->whereIn("tipo",$tipos)
                ->whereIn("area",$areas)->get();

        } else{
            $tipos = CtgTipoArchivoInvitacion::where("estatus","=",1)
                ->whereIn("tipo",$data["tipo"])
                ->whereIn("area",$data["area"])->get();
        }
        return $tipos;
    }
}
