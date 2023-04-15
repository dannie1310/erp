<?php

namespace App\Repositories\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDISATNomina;
use App\Models\SEGURIDAD_ERP\Fiscal\ConceptosCFDIEmitidos;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;


class ConceptosCFDIEmitidosRepository extends Repository implements RepositoryInterface
{
    public function __construct(ConceptosCFDIEmitidos $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function registrar(array $datos)
    {
        return $this->model->registrar($datos);

    }

    public function validaExistencia($uuid)
    {
        $cfdi_nomina = ConceptosCFDIEmitidos::where("uuid", "=", $uuid)->first();
        return $cfdi_nomina;
    }

}
