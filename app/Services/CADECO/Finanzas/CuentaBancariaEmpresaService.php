<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 30/05/2019
 * Time: 05:29 PM
 */

namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\CuentaBancariaEmpresa;
use App\Repositories\Repository;

class CuentaBancariaEmpresaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CuentaBancariaEmpresaService constructor.
     * @param CuentaBancariaEmpresa $model
     */
    public function __construct(CuentaBancariaEmpresa $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate($data)
    {
        return $this->repository->withoutGlobalScopes()->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->withoutGlobalScopes()->show($id);
    }
}