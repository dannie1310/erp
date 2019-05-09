<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/05/2019
 * Time: 02:16 PM
 */

namespace App\Services\CADECO\Compras;


use App\Models\CADECO\OrdenCompra;
use App\Repositories\Repository;

class OrdenCompraService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(OrdenCompra $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

}