<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 11:13 AM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\TipoCuentaContable;
use App\Repositories\Repository;

class TipoCuentaContableService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TipoCuentaContableService constructor.
     * @param TipoCuentaContable $model
     */
    public function __construct(TipoCuentaContable $model)
    {
        $this->repository = new Repository($model);
    }

    public function index()
    {
        return $this->repository->all();
    }
}