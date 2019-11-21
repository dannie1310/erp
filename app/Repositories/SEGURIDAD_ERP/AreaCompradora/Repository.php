<?php


namespace App\Repositories\SEGURIDAD_ERP\AreaCompradora;

use App\Models\SEGURIDAD_ERP\Compras\CtgAreaCompradora as Model;
use App\Models\SEGURIDAD_ERP\Compras\AreaCompradoraUsuario;

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
        $usuario = new AreaCompradoraUsuario();
        $usuario->asignar($data);
    }
}
