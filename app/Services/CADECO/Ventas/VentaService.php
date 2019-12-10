<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/12/2019
 * Time: 08:16 PM
 */

namespace App\Services\CADECO\Ventas;

use App\Models\CADECO\Venta;
use App\Repositories\Repository;

class VentaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * VentaService constructor
     * @param Venta $model
     */
    public function __construct(Venta $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

}
