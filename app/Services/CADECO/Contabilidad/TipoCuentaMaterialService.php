<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 11/02/19
 * Time: 01:11 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\TipoCuentaMaterial;
use App\Repositories\Repository;

class TipoCuentaMaterialService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TipoCuentaMaterialService constructor.
     *
     * @param TipoCuentaMaterial $model
     */
    public function __construct(TipoCuentaMaterial $model)
    {
        $this->repository = new Repository($model);
    }

    public function index()
    {
        return $this->repository->all();
    }
}