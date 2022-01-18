<?php

namespace App\Repositories\SEGURIDAD_ERP\PadronProveedores;

use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivo as Model;

class InvitacionArchivoRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getInvitacion($id_invitacion){
        $invitacion = Invitacion::find($id_invitacion);
        return "Invitación a ".$invitacion->tipo_invitacion." ".$invitacion->numero_folio_format.' '.$invitacion->observaciones;
    }

}
