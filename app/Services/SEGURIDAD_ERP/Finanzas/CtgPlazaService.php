<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/08/2019
 * Time: 07:19 PM
 */

namespace App\Services\SEGURIDAD_ERP\Finanzas;


use App\Models\SEGURIDAD_ERP\Finanzas\CtgPlaza;
use App\Repositories\Repository;

class CtgPlazaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CtgPlazaService constructor.
     * @param CtgPlaza $model
     */
    public function __construct(CtgPlaza $model)
    {
        $this->repository = new Repository($model);
    }

    public function index()
    {
        return $this->repository->all();
    }
}