<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 11/02/19
 * Time: 01:11 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\TipoCuentaEmpresa;
use App\Repositories\Repository;

class TipoCuentaEmpresaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TipoCuentaEmpresaService constructor.
     * @param TipoCuentaEmpresa $model
     */
    public function __construct(TipoCuentaEmpresa $model)
    {
        $this->repository = new Repository($model);
    }

    public function index()
    {
        return $this->repository->all();
    }
}