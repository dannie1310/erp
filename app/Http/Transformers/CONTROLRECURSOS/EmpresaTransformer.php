<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\Empresa;
use League\Fractal\TransformerAbstract;

class EmpresaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'cuentas',
        'cuentas_pagadoras_santander'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(Empresa $model){
        return [
            'id' => $model->getKey(),
            'razon_social' => $model->RazonSocial,
            'rfc' => $model->RFC
        ];
    }

    /**
     * @param Empresa $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeCuentas(Empresa $model)
    {
        if($cuenta = $model->cuentasEmpresa)
        {
            return $this->collection($cuenta, new CuentaEmpresaTransformer);
        }
        return null;
    }

    public function includeCuentasPagadorasSantander(Empresa $model)
    {
        if($cuenta = $model->cuentasPagadorasSantanderEmpresa)
        {
            return $this->collection($cuenta, new CuentaEmpresaTransformer);
        }
        return null;
    }
}
