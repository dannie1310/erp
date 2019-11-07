<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 10/10/2019
 * Time: 08:59 PM
 */

namespace App\Repositories\CADECO\EntradaAlmacen;


use App\Models\CADECO\EntradaMaterial;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    /**
     * @var EntradaMaterial
     */
    protected $model;

    /**
     * RepositoryInterface constructor.
     * @param EntradaMaterial $model
     */
    public function __construct(EntradaMaterial $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->registrar($data);
    }
}