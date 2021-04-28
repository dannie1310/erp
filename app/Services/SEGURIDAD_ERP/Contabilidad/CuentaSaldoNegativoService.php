<?php

namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\CuentaSaldoNegativo;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\CuentaSaldoNegativoRepository as Repository;

class CuentaSaldoNegativoService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(CuentaSaldoNegativo $model)
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

    public function sincronizar()
    {
        return $this->repository->sincronizar();
    }
}
