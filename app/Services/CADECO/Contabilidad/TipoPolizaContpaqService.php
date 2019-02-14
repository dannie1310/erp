<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/01/19
 * Time: 06:23 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\TipoPolizaContpaq;
use App\Repositories\Repository;

class TipoPolizaContpaqService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TipoPolizaContpaqService constructor.
     * @param TipoPolizaContpaq $model
     */
    public function __construct(TipoPolizaContpaq $model)
    {
        $this->repository = new Repository($model);
    }

    public function index()
    {
        return $this->repository->all();
    }
}