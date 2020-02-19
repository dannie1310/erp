<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:42 AM
 */

namespace App\Services\CTPQ;


use App\Models\CTPQ\Empresa;
use App\Repositories\Repository;
class EmpresaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * AlmacenService constructor.
     *
     * @param Almacen $model
     */
    public function __construct(Empresa $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

}