<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 02/05/2019
 * Time: 06:11 PM
 */

namespace App\Repositories\SEGURIDAD_ERP\AreaCompradora;

use App\Facades\Context;
use App\Models\SEGURIDAD_ERP\TipoAreaCompradora as Model;
use App\Models\CADECO\Inventarios\Marbete;
use App\Models\SEGURIDAD_ERP\UsuarioAreaCompradora;
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
        $usuario = new UsuarioAreaCompradora();
        $usuario->asignar($data);
    }
}
