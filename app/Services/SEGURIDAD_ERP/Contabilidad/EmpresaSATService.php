<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 26/02/2020
 * Time: 03:30 PM
 */

namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\EmpresaSATRepository as Repository;

class EmpresaSATService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(EmpresaSAT $model)
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

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function buscaPorRFC($rfc)
    {
        $this->repository->where([["rfc","=",$rfc]]);
        return $this->repository->first();
    }
}
