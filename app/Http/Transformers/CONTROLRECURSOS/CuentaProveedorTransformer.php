<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\CuentaProveedor;
use League\Fractal\TransformerAbstract;

class CuentaProveedorTransformer  extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(CuentaProveedor $model){
        return [
            'id' => $model->getKey(),
            'numero_cuenta' => $model->Cuenta,
            'banco_nombre' => $model->bancoCuenta,
            'id_banco' => $model->IdBanco,
            'cve_banco' => $model->banco_cve,
            'banco_descripcion' => $model->descripcion_banco
        ];
    }
}
