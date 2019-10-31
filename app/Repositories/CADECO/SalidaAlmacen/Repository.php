<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/09/2019
 * Time: 01:24 PM
 */

namespace App\Repositories\CADECO\SalidaAlmacen;

use App\Models\CADECO\Inventario;
use App\Models\CADECO\SalidaAlmacen as Model;

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
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->registrar($data);
    }
}