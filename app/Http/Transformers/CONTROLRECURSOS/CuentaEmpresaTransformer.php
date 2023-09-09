<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\CuentaEmpresa;
use League\Fractal\TransformerAbstract;

class CuentaEmpresaTransformer extends TransformerAbstract
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

    public function transform(CuentaEmpresa $model){
        return [
            'id_cuenta' => $model->IdCuentaBancaria,
            'numero_cuenta' => $model->numero_cuenta,
            'banco_descripcion' => $model->banco_descripcion,
            'tipo_cuenta' => $model->tipo_cuenta,
            'id_banco' => $model->id_banco
        ];
    }
}
