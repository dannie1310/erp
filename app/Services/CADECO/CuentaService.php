<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 30/01/19
 * Time: 07:45 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Cuenta;
use App\Repositories\Repository;

class CuentaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CuentaService constructor.
     * @param Cuenta $model
     */
    public function __construct(Cuenta $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }
}