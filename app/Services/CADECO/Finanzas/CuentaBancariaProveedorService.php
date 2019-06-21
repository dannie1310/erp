<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 30/05/2019
 * Time: 05:29 PM
 */

namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\CuentaBancariaProveedor;
use App\Repositories\Repository;

class CuentaBancariaProveedorService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CuentaBancariaProveedorService constructor.
     * @param CuentaBancariaProveedor $model
     */
    public function __construct(CuentaBancariaProveedor $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }
}