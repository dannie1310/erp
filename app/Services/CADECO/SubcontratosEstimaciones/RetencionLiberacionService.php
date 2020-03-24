<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 10/02/2020
 * Time: 18:41 PM
 */

namespace App\Services\CADECO\SubcontratosEstimaciones;

use App\Repositories\Repository;
use App\Models\CADECO\SubcontratosEstimaciones\Liberacion;


class RetencionLiberacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * RetencionLiberacionService constructor.
     * @param Venta $model
     */
    public function __construct(Liberacion $model)
    {
        $this->repository = new Repository($model);
    }

    public function delete($data, $id)
    {
        return $this->repository->delete($data, $id);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function store(array $data)
    {
        return $this->repository->create($data);
    }
}
