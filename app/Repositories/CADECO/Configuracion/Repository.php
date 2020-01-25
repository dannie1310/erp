<?php
/**
 * Created by PhpStorm.
 * User: jlopeza
 * Date: 30/12/2019
 * Time: 12:30 PM
 */

namespace App\Repositories\CADECO\Configuracion;

use Illuminate\Database\Eloquent\Model;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * RepositoryInterface constructor.
     * @param NodoTipo $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function delete($data, $id){
        $this->model->eliminar_asignacion($id);
    }
}