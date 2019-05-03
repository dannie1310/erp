<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 04/03/2019
 * Time: 01:36 PM
 */

namespace App\Services\CADECO\Contabilidad;

use App\Models\CADECO\Contabilidad\NaturalezaPoliza;
use App\Repositories\Repository;


class NaturalezaPolizaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TipoCuentaEmpresaService constructor.
     * @param NaturalezaPoliza $model
     */
    public function __construct(NaturalezaPoliza $model)
    {
        $this->repository = new Repository($model);
    }

    public function index()
    {
        return $this->repository->all();
    }
}