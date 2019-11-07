<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 30/01/19
 * Time: 07:23 PM
 */

namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Tesoreria\TipoMovimiento;
use App\Repositories\Repository;

class TipoMovimientoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TipoMovimientoService constructor.
     * @param TipoMovimiento $model
     */
    public function __construct(TipoMovimiento $model)
    {
        $this->repository = new Repository($model);
    }

    public function index()
    {
        return $this->repository->all();
    }
}