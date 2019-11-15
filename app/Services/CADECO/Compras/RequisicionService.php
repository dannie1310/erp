<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 14/11/2019
 * Time: 05:42 PM
 */

namespace App\Services\CADECO\Compras;


use App\Models\CADECO\Requisicion;
use App\Repositories\Repository;

class RequisicionService
{
    /**
     * @var Repository
     */
    protected $repsitory;

    /**
     * RequisicionService constructor.
     * @param Requisicion $model
     */
    public function __construct(Requisicion $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

}