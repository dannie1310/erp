<?php


namespace App\Repositories\SEGURIDAD_ERP\AreaSolicitante;

use App\Facades\Context;
use App\Models\SEGURIDAD_ERP\Compras\CtgAreaSolicitante as Model;
use App\Models\SEGURIDAD_ERP\Compras\AreaSolicitanteUsuario;
use Illuminate\Support\Facades\DB;

class Repository extends \App\Repositories\Repository  implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;


    /**
     * RepositoryInterface constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function asignar($data)
    {
        $usuario = new AreaSolicitanteUsuario();
        $usuario->asignar($data);
    }
}
