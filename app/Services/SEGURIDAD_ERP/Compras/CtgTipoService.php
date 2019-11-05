<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 24/10/2019
 * Time: 06:15 PM
 */


namespace App\Services\SEGURIDAD_ERP\Compras;


use App\Models\SEGURIDAD_ERP\Compras\CtgTipo;
use App\Repositories\Repository;

class CtgTipoService
{

    /**
     * @var Repository
     */
    protected $repository;


    /**
     * CtgTipoService constructor
     * @param CtgTipo $model
     */

    public function __construct(CtgTipo $model)
    {
        $this->repository = new Repository($model);
    }


    public function index($data)
    {

        return $this->repository->all($data);
    }

}
