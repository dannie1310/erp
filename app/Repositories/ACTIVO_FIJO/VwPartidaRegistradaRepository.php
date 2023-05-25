<?php

namespace App\Repositories\ACTIVO_FIJO;

use App\Models\SCI\VwPartidaRegistrada;
use App\Repositories\RepositoryInterface;

class VwPartidaRegistradaRepository extends \App\Repositories\Repository implements RepositoryInterface
{
    public function __construct(VwPartidaRegistrada $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getPorUsuario($data)
    {
        return $this->model->buscarPorUsuario($data)->get();
    }

    public function getPorCodigo($data)
    {
        return $this->model->buscarPorCodigo($data)->get();
    }

    public function getPorDepartamento($data)
    {
        return $this->model->buscarPorDepartamento($data)->get();
    }

    public function getPorReferencia($data)
    {
        return $this->model->buscarPorReferencia($data)->get();
    }

    public function getPorProyecto($data)
    {
        return $this->model->buscarPorUbicacion($data)->get();
    }
}
