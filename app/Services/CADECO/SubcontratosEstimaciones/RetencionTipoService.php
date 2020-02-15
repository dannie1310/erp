<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 11/02/2020
 * Time: 11:15 AM
 */

namespace App\Services\CADECO\SubcontratosEstimaciones;

use App\Repositories\Repository;
use App\Models\CADECO\SubcontratosEstimaciones\RetencionTipo;


class RetencionTipoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * RetencionService constructor.
     * @param Venta $model
     */
    public function __construct(RetencionTipo $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all();
    }
}