<?php

namespace App\Http\Transformers\CADECO;


use App\Http\Transformers\CADECO\Contabilidad\CuentaEmpresaTransformer;
use App\Http\Transformers\CADECO\Finanzas\CuentaBancariaEmpresaTransformer;
use App\Models\CADECO\Empresa;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\CADECO\Contrato\SubcontratoTransformer;

class EmpresaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'cuentasEmpresa',
        'cuentas',
        'subcontratos',
        'cuentasBancariasProveedor'
    ];

    public function transform(Empresa $model)
    {
        return [
            'id' => $model->getKey(),
            'razon_social' => $model->razon_social
        ];
    }

    /**
     * @param Empresa $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeCuentasEmpresa(Empresa $model)
    {
        if ($cuentas = $model->cuentasEmpresa) {
            return $this->collection($cuentas, new CuentaEmpresaTransformer);
        }
        return null;
    }

    /**
     * @param Empresa $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeCuentas(Empresa $model)
    {
        if($cuentas = $model->cuentas)
        {
            return $this->collection($cuentas, new CuentaTransformer);
        }
        return null;
    }

    /**
     * @param Empresa $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeSubcontratos(Empresa $model)
    {
        if($subcontratos = $model->subcontrato)
        {
            return $this->collection($subcontratos, new SubcontratoTransformer);
        }
        return null;
    }

    /**
     * @param Empresa $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeCuentasBancariasProveedor(Empresa $model){
        if($cuenta = $model->cuentaProveedor){
            return $this->collection($cuenta, new CuentaBancariaEmpresaTransformer);
        }
        return null;
    }
}