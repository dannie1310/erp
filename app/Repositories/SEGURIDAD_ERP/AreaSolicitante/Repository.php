<?php


namespace App\Repositories\SEGURIDAD_ERP\AreaSolicitante;

use App\Facades\Context;
use App\Models\SEGURIDAD_ERP\TipoAreaSolicitante as Model;
use App\Models\SEGURIDAD_ERP\UsuarioAreaSolicitante;
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
        $usuario = new UsuarioAreaSolicitante();
        $usuario->asignar($data);
    }
}
