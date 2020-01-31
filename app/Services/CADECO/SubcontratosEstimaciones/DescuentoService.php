<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 30/01/2020
 * Time: 12:40 PM
 */

namespace App\Services\CADECO\SubcontratosEstimaciones;


use App\Models\CADECO\SubcontratosEstimaciones\Descuento;
use App\Repositories\CADECO\SubcontratosEstimaciones\Descuento\Repository;

class DescuentoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * VentaServiceService constructor.
     * @param Venta $model
     */
    public function __construct(Descuento $model)
    {
        $this->repository = new Repository($model);
    }

    public function list($id)
    {  
        return $this->repository->list($id);
    }

    public function store(array $data)
    {
        return $this->repository->create($data);
    }
}