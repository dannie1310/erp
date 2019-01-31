<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/01/2019
 * Time: 12:19 PM
 */

namespace App\Repositories\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\CuentaMaterial;
use App\Traits\RepositoryTrait;

class CuentaMaterialRepository
{
    use RepositoryTrait;

    /**
     * @var CuentaMaterial
     */
    private $model;

    /**
     * CuentaMaterialRepository constructor.
     * @param CuentaMaterial $model
     */
    public function __construct(CuentaMaterial $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
}