<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\CtgMoneda;
use League\Fractal\TransformerAbstract;

class MonedaTransformer extends TransformerAbstract
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

    public function transform(CtgMoneda $model){
        return [
            'id' => $model->getKey(),
            'moneda' => $model->moneda,
            'corto' => $model->corto,
            'tipo_cambio' => $model->tipo_cambio
        ];
    }
}
