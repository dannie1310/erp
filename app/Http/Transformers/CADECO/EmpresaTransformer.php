<?php

namespace App\Http\Transformers\CADECO;


use App\Http\Transformers\CADECO\Contabilidad\CuentaEmpresaTransformer;
use App\Models\CADECO\Empresa;
use League\Fractal\TransformerAbstract;

class EmpresaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'cuentasEmpresa',
        'cuentas'
    ];

    public function transform(Empresa $model)
    {
        return [
            'id' => $model->getKey(),
            'razon_social' => $model->razon_social
        ];
    }

    public function includeCuentasEmpresa(Empresa $model)
    {
        if ($cuentas = $model->cuentasEmpresa) {
            return $this->collection($cuentas, new CuentaEmpresaTransformer);
        }
        return null;
    }

    public function includeCuentas(Empresa $model)
    {
        if($cuentas = $model->cuentas)
        {
            return $this->collection($cuentas, new CuentaTransformer);
        }
        return null;
    }
}