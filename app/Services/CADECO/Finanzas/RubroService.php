<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/01/2020
 * Time: 07:57 PM
 */

namespace App\Services\CADECO\Finanzas;
use App\Models\CADECO\Finanzas\Rubro;
use App\Repositories\Repository;

class RubroService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * RubroService constructor.
     * @param Rubro $model
     */
    public function __construct(Rubro $model)
    {
        $this->repository = new Repository($model);
    }

    public function index()
    {
        return $this->repository->all();
    }

}