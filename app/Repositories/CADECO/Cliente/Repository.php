<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 07/01/2020
 * Time: 07:13 PM
 */

namespace App\Repositories\CADECO\Cliente;

use App\Models\CADECO\Cliente as Model;

class Repository  extends \App\Repositories\Repository implements RepositoryInterface
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