<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 16/12/2019
 * Time: 06:23 PM
 */

namespace App\Repositories\CADECO\Venta;


use App\Models\CADECO\Venta;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{

    /**
     * @var Venta
     */
    protected $model;

    /**
     * RepositoryInterface constructor.
     * @param Venta $model
     */
    public function __construct(Venta $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->registrar($data);
    }
}