<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/09/2019
 * Time: 12:39 PM
 */

namespace App\Repositories\CADECO\Almacen;

use App\Models\CADECO\Almacen;
use App\Repositories\RepositoryInterface;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * RepositoryInterface constructor.
     * @param Model $model
     */
    public function __construct(Almacen $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->registrar($data);
    }

    public function findAlmacen(array $data)
    {
       return $this->model->where('descripcion', $data['descripcion'])->where('tipo_almacen', $data['tipo_almacen'])->first();
    }
}
