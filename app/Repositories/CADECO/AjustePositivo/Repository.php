<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 12/09/2019
 * Time: 01:21 PM
 */

namespace App\Repositories\CADECO\AjustePositivo;

use App\Models\CADECO\AjustePositivo as Model;

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

    public function busca($data)
    {
        return $this->model->buscaMaterial($data);
    }
}