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
        if (isset($data['base_datos'])) {
            $this->repository->where([['base_datos', 'LIKE', '%' . $data['base_datos'] . '%']]);
        }
        if (isset($data['nombre_empresa'])) {
            $this->repository->where([['nombre_empresa', 'LIKE', '%' . $data['nombre_empresa'] . '%']]);
        }
        if (isset($data['codigo_cuenta'])) {
            $this->repository->where([['codigo_cuenta', 'LIKE', '%' . $data['codigo_cuenta'] . '%']]);
        }
        if (isset($data['tipo'])) {
            $this->repository->where([['tipo', '=', $data['tipo']]]);
        }
        if (isset($data['nombre_cuenta'])) {
            $this->repository->where([['nombre_cuenta', 'LIKE', '%' . $data['nombre_cuenta'] . '%']]);
        }
        return $this->repository->paginate($data);
    }

    public function sincronizar()
    {
        return $this->repository->sincronizar();
    }

    public function obtenerInforme($id)
    {
        return $this->repository->obtenerInforme($id);
    }

    public function obtenerInformeMovimientos($data)
    {
        return $this->repository->obtenerInformeMovimientos($data);
    }
}
