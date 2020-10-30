<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 25/02/2019
 * Time: 06:58 PM
 */

namespace App\Services\CADECO\Contratos;


use App\Models\CADECO\Subcontrato;
use App\Repositories\CADECO\Subcontratos\Subcontrato\Repository;

class SubcontratoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SubcontratoService constructor.
     * @param Subcontrato $model
     */
    public function __construct(Subcontrato $model)
    {
        $this->repository = new Repository($model);
    }

    public function all($data)
    {
        return $this->repository->all($data);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store($data)
    {
        return $this->repository->create($data);
    }

    public function ordenado($id)
    {
        return $this->show($id)->subcontratoParaEstimar(null);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function pdf($id)
    {
        return $this->repository->show($id)->pdf();
    }
}
