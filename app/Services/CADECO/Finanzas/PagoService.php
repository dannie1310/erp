<?php

/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 15/08/19
 * Time: 12:34 PM
 */
namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Pago;
use App\Repositories\Repository;

class PagoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * PagoServicevice constructor
     *
     * @param Pago $model
     */

    public function __construct(Pago $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data){
        return $this->repository->paginate();
    }

}
